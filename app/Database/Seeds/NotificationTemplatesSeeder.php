<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationTemplatesSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Emergency Alert - Typhoon',
                'category' => 'Emergency',
                'title' => 'TYPHOON ALERT: {typhoon_name}',
                'message' => 'Dear {first_name}, Signal No. {signal_no} has been raised in our area due to Typhoon {typhoon_name}. Please stay indoors and prepare emergency supplies. Monitor official announcements. Stay safe!',
                'variables' => json_encode(['first_name', 'typhoon_name', 'signal_no']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Emergency Alert - Flood',
                'category' => 'Emergency',
                'title' => 'FLOOD WARNING',
                'message' => 'Dear {first_name}, Heavy rainfall has caused flooding in low-lying areas. If you are in {sitio}, please evacuate to higher ground immediately. Evacuation center is at the Barangay Hall.',
                'variables' => json_encode(['first_name', 'sitio']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Event Invitation - Community Meeting',
                'category' => 'Events',
                'title' => 'Community Meeting Invitation',
                'message' => 'Dear {first_name}, You are invited to attend our Barangay Assembly on {date} at {time} at the Barangay Hall. Your presence is important. See you there!',
                'variables' => json_encode(['first_name', 'date', 'time']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Event Invitation - Health Program',
                'category' => 'Events',
                'title' => 'Free Health Check-up Program',
                'message' => 'Dear {first_name}, Free health check-up and vaccination program on {date} at {time}. Bring your health card. First come, first served. Limited slots available!',
                'variables' => json_encode(['first_name', 'date', 'time']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Payment Reminder - Business Permit',
                'category' => 'Reminders',
                'title' => 'Business Permit Renewal Reminder',
                'message' => 'Dear {first_name}, Your business permit for {business_name} is due for renewal on {due_date}. Please visit the Barangay Hall to process your renewal. Penalty applies after due date.',
                'variables' => json_encode(['first_name', 'business_name', 'due_date']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Certificate Ready for Pickup',
                'category' => 'Reminders',
                'title' => 'Certificate Ready',
                'message' => 'Dear {first_name}, Your {certificate_type} is now ready for pickup. Please visit the Barangay Hall during office hours (Mon-Fri, 8AM-5PM). Bring a valid ID.',
                'variables' => json_encode(['first_name', 'certificate_type']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Hearing Reminder',
                'category' => 'Reminders',
                'title' => 'Hearing Schedule Reminder',
                'message' => 'Dear {first_name}, You have a scheduled hearing on {date} at {time} for Case No. {case_number}. Please attend at the Barangay Hall. Failure to attend may result in default judgment.',
                'variables' => json_encode(['first_name', 'date', 'time', 'case_number']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Birthday Greeting',
                'category' => 'Greetings',
                'title' => 'Happy Birthday {first_name}!',
                'message' => 'Happy Birthday {first_name}! The Barangay {barangay_name} wishes you good health, happiness, and prosperity. May you have a wonderful celebration!',
                'variables' => json_encode(['first_name', 'barangay_name']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'General Announcement',
                'category' => 'Announcements',
                'title' => 'Barangay Announcement',
                'message' => 'Dear {first_name}, {announcement_message}. For more information, please contact the Barangay Hall or visit our office.',
                'variables' => json_encode(['first_name', 'announcement_message']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Garbage Collection Schedule',
                'category' => 'Announcements',
                'title' => 'Garbage Collection Reminder',
                'message' => 'Dear {first_name}, Garbage collection in {sitio} is scheduled for {collection_day}. Please segregate your waste: Biodegradable, Non-biodegradable, and Recyclables. Thank you for your cooperation!',
                'variables' => json_encode(['first_name', 'sitio', 'collection_day']),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('notification_templates')->insertBatch($templates);
    }
}
