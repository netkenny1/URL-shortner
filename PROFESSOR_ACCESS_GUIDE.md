# Accessing the ShortKenny Application on Google Cloud Run

This guide explains how to access and view the deployed ShortKenny URL shortener application on Google Cloud Platform.

## Application Access

**Live Application URL:** https://shortkenny-644197836082.europe-west1.run.app

The application is publicly accessible. You can:
- Visit the web interface at the URL above
- Test all API endpoints (see API documentation in README.md)
- Create, view, and manage shortened URLs

## Google Cloud Console Access

You have been granted **Viewer** access to the Google Cloud project. This allows you to view:
- Cloud Run service configuration and status
- Application logs
- Metrics and performance data
- Deployment history

### Accessing Cloud Run Service

1. **Navigate to Cloud Run Console:**
   - Go to: https://console.cloud.google.com/run?project=deep-ray-479811-a3
   - Or visit: https://console.cloud.google.com/run
   - Select project: `deep-ray-479811-a3`

2. **View Service Details:**
   - Click on the service named `shortkenny`
   - You'll see:
     - Service status (Active)
     - Public URL
     - Region: `europe-west1`
     - Last deployment timestamp
     - Resource allocation (CPU, Memory)
     - Traffic allocation

3. **View Metrics:**
   - In the service details page, click the **"Metrics"** tab
   - View:
     - Request count over time
     - Request latency (P50, P95, P99 percentiles)
     - Error rate
     - Instance count (auto-scaling behavior)
     - CPU and Memory utilization

### Viewing Application Logs

1. **Navigate to Logs Viewer:**
   - Go to: https://console.cloud.google.com/logs?project=deep-ray-479811-a3
   - Or from Cloud Run service page, click **"Logs"** tab

2. **Filter Logs:**
   - Resource type: `Cloud Run Revision`
   - Service name: `shortkenny`
   - You'll see:
     - Application startup logs
     - HTTP request logs (GET, POST, etc.)
     - Health check logs (`/health` endpoint calls)
     - Error messages (if any)

### Viewing Deployment History

1. **In Cloud Run Service Page:**
   - Scroll to **"Revisions"** section
   - View all deployed revisions
   - See deployment timestamps
   - View traffic allocation (which revision receives traffic)

## Project Information

- **Project ID:** `deep-ray-479811-a3`
- **Service Name:** `shortkenny`
- **Region:** `europe-west1`
- **Platform:** Google Cloud Run (serverless)
- **Container Registry:** Google Container Registry (GCR)

## What You Can View

With Viewer access, you can:
- ✅ View all Cloud Run services and their configurations
- ✅ View application logs and error messages
- ✅ View metrics and performance data
- ✅ View deployment history and revisions
- ✅ Access service URLs
- ❌ Cannot modify or delete services
- ❌ Cannot view billing information

## Testing the Application

### Web Interface
Visit: https://shortkenny-644197836082.europe-west1.run.app

### API Endpoints
All endpoints are publicly accessible. See `README.md` for complete API documentation.

**Health Check:**
```bash
curl https://shortkenny-644197836082.europe-west1.run.app/health
```

**Metrics:**
```bash
curl https://shortkenny-644197836082.europe-west1.run.app/metrics
```

**Create Short Link:**
```bash
curl -X POST https://shortkenny-644197836082.europe-west1.run.app/api/links \
  -H "Content-Type: application/json" \
  -d '{"original_url": "https://example.com"}'
```

## Troubleshooting

**Cannot access Cloud Console:**
- Ensure you're logged into the Google account that received the invitation
- Check that you accepted the IAM policy binding invitation
- Verify you're accessing: https://console.cloud.google.com/run?project=deep-ray-479811-a3

**Cannot see the service:**
- Make sure you've selected the correct project: `deep-ray-479811-a3`
- Check that you're in the correct region view (or use "All regions")

**Need more information:**
- See `README.md` for application documentation
- See `REPORT.md` for detailed implementation report
- Contact the student for additional access if needed
