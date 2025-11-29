# Azure Deployment Guide

This guide walks you through deploying ShortKenny to Azure using Docker containerization.

## Prerequisites

- Azure account (free tier available: https://azure.microsoft.com/free/)
- Azure CLI installed (or use Azure Cloud Shell)
- GitHub repository with your code

## Step-by-Step Setup

### Step 1: Create Azure Account

1. Go to: https://azure.microsoft.com/free/
2. Click "Start free" or "Create Azure free account"
3. Sign up with your email (or use existing Microsoft account)
4. Complete verification if required

### Step 2: Install Azure CLI (Optional - or use Cloud Shell)

**On Mac:**
```bash
brew install azure-cli
```

**On Windows:**
Download from: https://aka.ms/installazurecliwindows

**Or use Azure Cloud Shell:**
- Go to: https://shell.azure.com
- No installation needed!

### Step 3: Login to Azure

```bash
az login
```

This will open a browser for authentication.

### Step 4: Create Resource Group

```bash
az group create --name shortkenny-rg --location eastus
```

(Replace `eastus` with your preferred location: `westus`, `westeurope`, etc.)

### Step 5: Create Azure Container Registry (ACR)

```bash
az acr create \
  --resource-group shortkenny-rg \
  --name shortkennyregistry \
  --sku Basic \
  --admin-enabled true
```

**Note:** Replace `shortkennyregistry` with a unique name (lowercase, numbers only). This is your registry name.

### Step 6: Get ACR Credentials

```bash
az acr credential show --name shortkennyregistry --resource-group shortkenny-rg
```

Save these values:
- `username`
- `passwords[0].value` (this is your password)
- Login server URL (format: `shortkennyregistry.azurecr.io`)

### Step 7: Create Service Principal for GitHub Actions

```bash
az ad sp create-for-rbac \
  --name "shortkenny-github-actions" \
  --role contributor \
  --scopes /subscriptions/$(az account show --query id -o tsv)/resourceGroups/shortkenny-rg \
  --sdk-auth
```

**Save the entire JSON output!** It looks like:
```json
{
  "clientId": "...",
  "clientSecret": "...",
  "subscriptionId": "...",
  "tenantId": "..."
}
```

### Step 8: Configure GitHub Secrets

1. Go to your GitHub repository: https://github.com/netkenny1/URL-shortner
2. Click **Settings** (top menu)
3. Click **Secrets and variables** → **Actions** (left sidebar)
4. Click **New repository secret** for each:

#### Secret 1: `AZURE_CREDENTIALS`
- **Name:** `AZURE_CREDENTIALS`
- **Value:** Paste the entire JSON from Step 7
- Click **Add secret**

#### Secret 2: `AZURE_RESOURCE_GROUP`
- **Name:** `AZURE_RESOURCE_GROUP`
- **Value:** `shortkenny-rg`
- Click **Add secret**

#### Secret 3: `AZURE_REGISTRY_LOGIN_SERVER`
- **Name:** `AZURE_REGISTRY_LOGIN_SERVER`
- **Value:** `shortkennyregistry.azurecr.io` (replace with your registry name)
- Click **Add secret**

#### Secret 4: `AZURE_REGISTRY_USERNAME`
- **Name:** `AZURE_REGISTRY_USERNAME`
- **Value:** The username from Step 6
- Click **Add secret**

#### Secret 5: `AZURE_REGISTRY_PASSWORD`
- **Name:** `AZURE_REGISTRY_PASSWORD`
- **Value:** The password from Step 6
- Click **Add secret**

#### Secret 6: `AZURE_DNS_NAME_LABEL`
- **Name:** `AZURE_DNS_NAME_LABEL`
- **Value:** `shortkenny-yourname` (must be unique, lowercase, no spaces)
- Click **Add secret**

#### Secret 7: `AZURE_LOCATION`
- **Name:** `AZURE_LOCATION`
- **Value:** `eastus` (or your chosen location)
- Click **Add secret**

### Step 9: Trigger Deployment

1. Go to your GitHub repository
2. Click **Actions** tab
3. You should see "CD Pipeline - Deploy to Azure"
4. The workflow will run automatically on push to `main`
5. Or click **Run workflow** to trigger manually

### Step 10: Access Your Deployed Application

After deployment completes:

1. Go to Azure Portal: https://portal.azure.com
2. Navigate to: **Resource groups** → **shortkenny-rg**
3. Click on **shortkenny** (Container Instance)
4. Find the **FQDN** (Fully Qualified Domain Name)
5. Your app will be at: `https://shortkenny-yourname.eastus.azurecontainer.io`

Or use Azure CLI:
```bash
az container show \
  --resource-group shortkenny-rg \
  --name shortkenny \
  --query ipAddress.fqdn \
  --output tsv
```

### Step 11: Test Your Deployment

- **Main app:** `https://shortkenny-yourname.eastus.azurecontainer.io`
- **Health endpoint:** `https://shortkenny-yourname.eastus.azurecontainer.io/health`
- **Metrics:** `https://shortkenny-yourname.eastus.azurecontainer.io/metrics`

## Troubleshooting

### Check Deployment Status

```bash
az container show \
  --resource-group shortkenny-rg \
  --name shortkenny \
  --query instanceView.state
```

### View Logs

```bash
az container logs \
  --resource-group shortkenny-rg \
  --name shortkenny
```

### Delete Resources (Cleanup)

```bash
az group delete --name shortkenny-rg --yes --no-wait
```

## Cost Information

- **Azure Container Registry Basic:** ~$5/month (first 10GB free)
- **Azure Container Instances:** Pay per second (~$0.000012/second for 1 CPU, 1GB RAM)
- **Estimated monthly cost:** ~$5-10 for light usage
- **Free tier:** $200 credit for first month

## Alternative: Azure App Service (More Features)

If you prefer Azure App Service instead of Container Instances:

1. Create App Service Plan:
```bash
az appservice plan create \
  --name shortkenny-plan \
  --resource-group shortkenny-rg \
  --sku B1 \
  --is-linux
```

2. Create Web App:
```bash
az webapp create \
  --resource-group shortkenny-rg \
  --plan shortkenny-plan \
  --name shortkenny-app \
  --deployment-container-image-name shortkennyregistry.azurecr.io/shortkenny:latest
```

3. Configure ACR credentials:
```bash
az webapp config container set \
  --name shortkenny-app \
  --resource-group shortkenny-rg \
  --docker-custom-image-name shortkennyregistry.azurecr.io/shortkenny:latest \
  --docker-registry-server-url https://shortkennyregistry.azurecr.io \
  --docker-registry-server-user <username> \
  --docker-registry-server-password <password>
```

## Documentation for Assignment

Include in your REPORT.md:

1. **Deployment Platform:** Azure Container Instances
2. **Container Registry:** Azure Container Registry (ACR)
3. **Deployment URL:** `https://shortkenny-yourname.eastus.azurecontainer.io`
4. **Screenshots:**
   - Azure Portal showing the container instance
   - GitHub Actions workflow running
   - Deployed application
   - Health endpoint response
5. **Explanation:** How the CI/CD pipeline builds the Docker image, pushes to ACR, and deploys to ACI automatically on push to main branch

