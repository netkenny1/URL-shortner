# Professor Access Guide - Google Cloud Run Service

This guide explains how to grant your professor access to view the deployed ShortKenny application on Google Cloud Run.

## Quick Access

**Application URL:** https://shortkenny-644197836082.europe-west1.run.app

The application is publicly accessible, but to view Cloud Run service details, logs, and metrics in the Google Cloud Console, the professor needs to be granted viewer access.

## Granting Access

### Step 1: Add Professor as Viewer

Run the following command in Google Cloud Shell or your local terminal (with gcloud CLI installed):

```bash
# Replace PROFESSOR_EMAIL with your professor's university email
PROFESSOR_EMAIL="professor@university.edu"
PROJECT_ID="deep-ray-479811-a3"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="user:${PROFESSOR_EMAIL}" \
  --role="roles/viewer"
```

### Step 2: Verify Access

After running the command, the professor will receive an email invitation. Once they accept, they can:

1. **View the Cloud Run Service:**
   - Navigate to: https://console.cloud.google.com/run?project=deep-ray-479811-a3
   - Click on the `shortkenny` service

2. **View Logs:**
   - Navigate to: https://console.cloud.google.com/logs?project=deep-ray-479811-a3
   - Filter by resource type: Cloud Run Revision
   - Filter by service name: shortkenny

3. **View Metrics:**
   - In the Cloud Run service dashboard, click on the "Metrics" tab
   - View request count, latency, error rate, etc.

## Access Levels

The `roles/viewer` role provides:
- ✅ View Cloud Run services and configurations
- ✅ View logs and metrics
- ✅ View deployment history
- ✅ View service URLs and status
- ❌ Cannot modify or delete services
- ❌ Cannot view billing information

## Alternative: Public Access Only

If you prefer not to grant console access, the professor can still:
- Access the application at: https://shortkenny-644197836082.europe-west1.run.app
- Test all API endpoints (publicly accessible)
- View the application functionality
- Review the GitHub repository for code and CI/CD pipelines

## Project Information

- **Project ID:** `deep-ray-479811-a3`
- **Service Name:** `shortkenny`
- **Region:** `europe-west1`
- **Platform:** Cloud Run (managed)

## Troubleshooting

If the professor cannot access the console:
1. Verify the email address is correct
2. Check that they accepted the invitation email
3. Ensure they're logged into the correct Google account
4. Try accessing: https://console.cloud.google.com/run?project=deep-ray-479811-a3

For any access issues, you can verify current permissions with:
```bash
gcloud projects get-iam-policy deep-ray-479811-a3
```

