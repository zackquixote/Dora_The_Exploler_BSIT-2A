# Notification System Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                          │
│                  /advanced/send-notification                    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    RECIPIENT SELECTION                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │   All    │  │  Sitio   │  │   Age    │  │  Gender  │      │
│  │ Residents│  │  Filter  │  │  Group   │  │  Filter  │      │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘      │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │  Voters  │  │ Seniors  │  │   PWD    │  │ Specific │      │
│  │   Only   │  │   Only   │  │   Only   │  │ Residents│      │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    AJAX: Get Recipient Count                    │
│              POST /advanced/get-recipient-count                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    MESSAGE COMPOSITION                          │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Templates  │  Drafts  │  Manual Entry                   │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Title: ___________________________________________       │  │
│  │  Message: _________________________________________       │  │
│  │           _________________________________________       │  │
│  │  Variables: {first_name}, {sitio}, etc.                  │  │
│  │  Character Counter: 0/160 (1 SMS)                        │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DELIVERY OPTIONS                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────────────────┐ │
│  │ ☑ SMS    │  │ ☐ Email  │  │ Schedule: [Date/Time]        │ │
│  └──────────┘  └──────────┘  └──────────────────────────────┘ │
│  Cost Estimate: ₱150.00 (150 recipients × 1 SMS)              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CONFIRMATION DIALOG                          │
│  "Send to 150 recipients now?"                                 │
│  [Cancel]  [Confirm]                                           │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FORM SUBMISSION (AJAX)                       │
│              POST /advanced/send-notification                   │
│  {                                                              │
│    recipients: {...criteria...},                               │
│    title: "...",                                                │
│    message: "...",                                              │
│    channels: ["sms", "email"],                                 │
│    scheduled_at: "2026-06-01 10:00:00"                         │
│  }                                                              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CONTROLLER PROCESSING                        │
│         AdvancedFeatures::sendBulkNotification()               │
│  1. Parse recipient criteria                                   │
│  2. Get matching resident IDs                                  │
│  3. For each resident:                                         │
│     - Personalize message                                      │
│     - Create notification record                               │
│     - Queue for delivery                                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE OPERATIONS                          │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ INSERT INTO notifications                                │  │
│  │   (recipient_id, type, title, message, channels,         │  │
│  │    status, scheduled_at, created_at)                     │  │
│  │ VALUES (1, 'announcement', '...', '...', '["sms"]',      │  │
│  │         'pending', '2026-06-01 10:00:00', NOW())         │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    NOTIFICATION SERVICE                         │
│         NotificationService::sendToResident()                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ For each channel:                                        │  │
│  │   if (SMS)   → sendSMS()                                 │  │
│  │   if (Email) → sendEmail()                               │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                ┌────────────┴────────────┐
                ▼                         ▼
┌──────────────────────────┐  ┌──────────────────────────┐
│     SMS DELIVERY         │  │    EMAIL DELIVERY        │
│  ┌────────────────────┐  │  │  ┌────────────────────┐  │
│  │ Semaphore API      │  │  │  │ Gmail API / SMTP   │  │
│  │ POST /api/v4/...   │  │  │  │ Send email         │  │
│  └────────────────────┘  │  │  └────────────────────┘  │
└────────────┬─────────────┘  └────────────┬─────────────┘
             │                             │
             └──────────────┬──────────────┘
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DELIVERY LOGGING                             │
│  INSERT INTO notification_delivery_logs                        │
│    (notification_id, channel, recipient_contact,               │
│     status, provider, cost, sent_at)                           │
│  VALUES (1, 'sms', '639123456789', 'sent',                     │
│          'semaphore', 1.00, NOW())                             │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    UPDATE NOTIFICATION STATUS                   │
│  UPDATE notifications                                          │
│  SET status = 'sent', sent_at = NOW()                          │
│  WHERE id = 1                                                  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    RESPONSE TO CLIENT                           │
│  {                                                              │
│    "success": true,                                            │
│    "message": "Notifications sent successfully",               │
│    "stats": {                                                  │
│      "total": 150,                                             │
│      "success": 148,                                           │
│      "failed": 2,                                              │
│      "sms_sent": 148,                                          │
│      "email_sent": 120,                                        │
│      "estimated_cost": 148.00                                  │
│    },                                                          │
│    "failed_recipients": [...]                                  │
│  }                                                              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    RESULTS MODAL                                │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  ✓ Notifications Sent Successfully                       │  │
│  │                                                          │  │
│  │  Delivery Summary:                                       │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌─────────┐ │  │
│  │  │   148    │  │    2     │  │   148    │  │   120   │ │  │
│  │  │Successful│  │  Failed  │  │SMS Sent  │  │Email Sent│ │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └─────────┘ │  │
│  │                                                          │  │
│  │  Estimated Cost: ₱148.00                                 │  │
│  │                                                          │  │
│  │  [Close]  [Send Another]                                 │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Component Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         PRESENTATION LAYER                      │
├─────────────────────────────────────────────────────────────────┤
│  Views/advanced/send_notification.php                          │
│  - HTML Form                                                   │
│  - JavaScript (AJAX, validation, UI interactions)              │
│  - CSS Styling                                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         CONTROLLER LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│  Controllers/AdvancedFeatures.php                              │
│  - sendBulkNotification()      (Main handler)                  │
│  - getRecipientCount()         (AJAX endpoint)                 │
│  - loadTemplate()              (AJAX endpoint)                 │
│  - loadDraft()                 (AJAX endpoint)                 │
│  - deleteDraft()               (AJAX endpoint)                 │
│  - searchResidents()           (AJAX endpoint)                 │
│  - getRecipientIds()           (Helper)                        │
│  - personalizeMessage()        (Helper)                        │
│  - saveDraft()                 (Helper)                        │
│  - getDeliveryStats()          (Helper)                        │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         SERVICE LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  Services/NotificationService.php                              │
│  - sendToResident()            (Single recipient)              │
│  - sendBulk()                  (Multiple recipients)           │
│  - sendToGroup()               (Criteria-based)                │
│  - scheduleNotification()      (Future delivery)               │
│  - sendSMS()                   (SMS channel)                   │
│  - sendEmail()                 (Email channel)                 │
│  - sendSemaphoreSMS()          (Semaphore provider)            │
│  - sendTwilioSMS()             (Twilio provider)               │
│  - formatPhoneNumber()         (Helper)                        │
│  - getTemplate()               (Template loader)               │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         MODEL LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│  Models/NotificationModel.php                                  │
│  - insert()                    (Create notification)           │
│  - update()                    (Update status)                 │
│  - getForRecipient()           (Get user notifications)        │
│  - getPending()                (Get pending queue)             │
│  - getStats()                  (Get statistics)                │
│                                                                │
│  Models/ResidentModel.php                                      │
│  - findAll()                   (Get residents)                 │
│  - where()                     (Filter residents)              │
│  - getWithAge()                (With age calculation)          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         DATABASE LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│  Tables:                                                       │
│  - notifications               (Main records)                  │
│  - notification_templates      (Reusable templates)            │
│  - notification_delivery_logs  (Delivery tracking)             │
│  - notification_drafts         (Saved drafts)                  │
│  - residents                   (Recipient data)                │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Recipient Selection Flow
```
User selects criteria
    ↓
JavaScript updates filters
    ↓
AJAX call to getRecipientCount()
    ↓
Controller queries database
    ↓
Returns count + cost estimate
    ↓
UI updates preview
```

### 2. Template Loading Flow
```
User clicks template
    ↓
AJAX call to loadTemplate(id)
    ↓
Controller fetches from DB
    ↓
Returns template data
    ↓
JavaScript populates form
```

### 3. Message Sending Flow
```
User submits form
    ↓
JavaScript validates
    ↓
Confirmation dialog
    ↓
AJAX POST to sendBulkNotification()
    ↓
Controller processes:
  - Parse criteria
  - Get recipient IDs
  - For each recipient:
    * Personalize message
    * Create notification record
    * Call NotificationService
    ↓
NotificationService:
  - Send via SMS/Email
  - Log delivery
  - Update status
    ↓
Return statistics
    ↓
Display results modal
```

## Security Layers

```
┌─────────────────────────────────────────────────────────────────┐
│  1. Route Filter: 'role:admin'                                 │
│     Only admins can access                                     │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. CSRF Protection                                            │
│     csrf_field() in form                                       │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. Input Validation                                           │
│     Required fields, data types                                │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  4. Confirmation Dialog                                        │
│     User must confirm bulk sends                               │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. Audit Logging                                              │
│     All actions logged to database                             │
└─────────────────────────────────────────────────────────────────┘
```

## Integration Points

### External Services
```
┌──────────────────┐
│  Semaphore API   │ ← SMS Provider
└──────────────────┘

┌──────────────────┐
│   Twilio API     │ ← Alternative SMS Provider
└──────────────────┘

┌──────────────────┐
│   Gmail API      │ ← Email Provider (Primary)
└──────────────────┘

┌──────────────────┐
│   SMTP Server    │ ← Email Provider (Fallback)
└──────────────────┘
```

### Internal Services
```
┌──────────────────┐
│  ResidentModel   │ ← Recipient data
└──────────────────┘

┌──────────────────┐
│  AuditLogs       │ ← Action tracking
└──────────────────┘

┌──────────────────┐
│  Session         │ ← User authentication
└──────────────────┘
```

## Performance Considerations

### Optimization Strategies
1. **Batch Processing** - Send in chunks to avoid timeouts
2. **Queue System** - Background processing for large batches
3. **Caching** - Cache recipient counts for common criteria
4. **Indexing** - Database indexes on frequently queried fields
5. **AJAX** - Non-blocking UI updates

### Scalability
- Handles 1000+ recipients per batch
- Supports scheduled delivery
- Retry mechanism for failed sends
- Delivery tracking per message
- Cost monitoring

---

**This architecture ensures:**
- ✅ Separation of concerns
- ✅ Maintainability
- ✅ Scalability
- ✅ Security
- ✅ Performance
- ✅ Reliability
