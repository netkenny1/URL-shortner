# Google Cloud Platform Deployment Guide

This guide walks you through deploying ShortKenny to Google Cloud Run using Docker containerization.

## Prerequisites

- Google Cloud account (free tier available: https://cloud.google.com/free/)
- Google Cloud project
- GitHub repository with your code

## Step-by-Step Setup

### Step 1: Create Google Cloud Account

1. Go to: https://cloud.google.com/free/
2. Click "Get started for free"
3. Sign up with your Google account
4. Complete verification (credit card may be required but won't be charged for free tier)

### Step 2: Create a GCP Project

1. Go to: https://console.cloud.google.com/
2. Click the project dropdown at the top
3. Click "New Project"
4. Enter project name: `shortkenny-project` (or your preferred name)
5. Click "Create"
6. **Note your Project ID** (shown in the project dropdown)

### Step 3: Enable Required APIs

1. Go to: https://console.cloud.google.com/apis/library
2. Enable these APIs (search and click "Enable"):
   - **Cloud Run API**
   - **Container Registry API** (or Artifact Registry API)
   - **Cloud Build API**

### Step 4: Create Service Account for GitHub Actions

1. Go to: https://console.cloud.google.com/iam-admin/serviceaccounts
2. Click "Create Service Account"
3. Enter:
   - **Name:** `github-actions`
   - **Description:** `Service account for GitHub Actions deployment`
4. Click "Create and Continue"
5. Grant roles:
   - **Cloud Run Admin**
   - **Cloud Build Editor** (for building container images)
   - **Service Account User**
   - **Storage Admin** (for Container Registry)
   - **Logs Viewer** (for streaming build logs)
6. Click "Continue" then "Done"

### Step 5: Create and Download Service Account Key

1. Click on the service account you just created (`github-actions`)
2. Go to "Keys" tab
3. Click "Add Key" → "Create new key"
4. Select "JSON"
5. Click "Create"
6. **Save the downloaded JSON file** - this is your service account key

### Step 6: Configure GitHub Secrets

1. Go to your GitHub repository: https://github.com/netkenny1/URL-shortner
2. Click **Settings** (top menu)
3. Click **Secrets and variables** → **Actions** (left sidebar)
4. Click **New repository secret** for each:

#### Secret 1: `GCP_SA_KEY`
- **Name:** `GCP_SA_KEY`
- **Value:** Open the JSON file from Step 5, copy the ENTIRE contents and paste here
- Click **Add secret**

#### Secret 2: `GCP_PROJECT_ID`
- **Name:** `GCP_PROJECT_ID`
- **Value:** Your project ID from Step 2 (e.g., `shortkenny-project-123456`)
- Click **Add secret**

#### Secret 3: `GCP_REGION`
- **Name:** `GCP_REGION`
- **Value:** `us-central1` (or your preferred region: `us-east1`, `europe-west1`, etc.)
- Click **Add secret**

### Step 7: Trigger Deployment

1. Go to your GitHub repository
2. Click **Actions** tab
3. You should see "CD Pipeline - Deploy to Google Cloud"
4. The workflow will run automatically on push to `main`
5. Or click **Run workflow** to trigger manually

### Step 8: Access Your Deployed Application

After deployment completes (about 2-3 minutes):

1. Go to: https://console.cloud.google.com/run
2. Click on your service: **shortkenny**
3. Find the **URL** in the service details
4. Your app will be at: `https://shortkenny-xxxxx-uc.a.run.app`

Or use gcloud CLI:
```bash
gcloud run services describe shortkenny \
  --platform managed \
  --region us-central1 \
  --format 'value(status.url)'
```

### Step 9: Test Your Deployment

- **Main app:** `https://shortkenny-xxxxx-uc.a.run.app`
- **Health endpoint:** `https://shortkenny-xxxxx-uc.a.run.app/health`
- **Metrics:** `https://shortkenny-xxxxx-uc.a.run.app/metrics`

## Using Google Cloud Shell (Alternative Setup)

If you prefer using Cloud Shell:

1. Go to: https://shell.cloud.google.com/
2. Run these commands:

```bash
# Set your project
gcloud config set project YOUR_PROJECT_ID

# Enable APIs
gcloud services enable run.googleapis.com
gcloud services enable containerregistry.googleapis.com
gcloud services enable cloudbuild.googleapis.com

# Create service account
gcloud iam service-accounts create github-actions \
  --display-name="GitHub Actions Service Account"

# Grant permissions
PROJECT_ID=$(gcloud config get-value project)
SA_EMAIL="github-actions@${PROJECT_ID}.iam.gserviceaccount.com"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/run.admin"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/cloudbuild.builds.editor"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/iam.serviceAccountUser"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/storage.admin"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/logging.viewer"

# Create and download key
gcloud iam service-accounts keys create key.json \
  --iam-account=$SA_EMAIL

# Download the key.json file
```

## Troubleshooting

### Cloud Build "Cannot Stream Logs" Error

If you see this error in GitHub Actions:
```
ERROR: (gcloud.builds.submit) 
The build is running, and logs are being written to the default logs bucket.
This tool can only stream logs if you are Viewer/Owner of the project...
```

**The build is actually running successfully!** The error is just about log streaming permissions.

**Fix:** Add the Logs Viewer role to your service account:
```bash
PROJECT_ID="YOUR_PROJECT_ID"
SA_EMAIL="github-actions@${PROJECT_ID}.iam.gserviceaccount.com"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:${SA_EMAIL}" \
  --role="roles/logging.viewer"
```

### Check Deployment Status

```bash
gcloud run services describe shortkenny \
  --platform managed \
  --region us-central1
```

### View Logs

```bash
gcloud run services logs read shortkenny \
  --platform managed \
  --region us-central1
```

### Delete Service (Cleanup)

```bash
gcloud run services delete shortkenny \
  --platform managed \
  --region us-central1
```

### Delete Project (Complete Cleanup)

1. Go to: https://console.cloud.google.com/iam-admin/settings
2. Click "Shut down" next to your project
3. Enter project ID to confirm

## Cost Information

- **Cloud Run:** Free tier includes:
  - 2 million requests per month
  - 360,000 GB-seconds of memory
  - 180,000 vCPU-seconds
- **Container Registry:** Free for first 0.5 GB storage
- **Estimated cost:** $0 for light usage (within free tier)
- **Free tier:** $300 credit for first 90 days

## Documentation for Assignment

Include in your REPORT.md:

1. **Deployment Platform:** Google Cloud Run
2. **Container Registry:** Google Container Registry (GCR)
3. **Deployment URL:** `https://shortkenny-xxxxx-uc.a.run.app`
4. **Screenshots:**
   - Google Cloud Console showing Cloud Run service
   - GitHub Actions workflow running
   - Deployed application
   - Health endpoint response
5. **Explanation:** How the CI/CD pipeline builds the Docker image, pushes to GCR, and deploys to Cloud Run automatically on push to main branch

