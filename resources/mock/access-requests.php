<?php

/* MOCK DATA (frontend phase only). Status: pending, approved, rejected. */
return [
    ['id' => 1, 'user' => 'Dr. Emily Davis', 'email' => 'emily.davis@cvsu.edu.ph',   'document' => 'Q4 financial report.xlsx',        'permission' => 'View only',         'date' => '2026-11-18', 'reason' => 'Needed to review research budget allocations before the grant submission.', 'status' => 'pending'],
    ['id' => 2, 'user' => 'Daniel Kim',      'email' => 'daniel.kim@cvsu.edu.ph',    'document' => 'Faculty handbook 2026.pdf',       'permission' => 'View and download', 'date' => '2026-11-17', 'reason' => 'Preparing the new-faculty orientation packet.',                              'status' => 'pending'],
    ['id' => 3, 'user' => 'Olivia Taylor',   'email' => 'olivia.taylor@cvsu.edu.ph', 'document' => 'Student enrollment report.xlsx', 'permission' => 'View only',         'date' => '2026-11-16', 'reason' => 'Required for the department planning meeting on Friday.',                  'status' => 'pending'],
    ['id' => 4, 'user' => 'Michael Brown',   'email' => 'michael.brown@cvsu.edu.ph', 'document' => 'Budget proposal FY 2027.xlsx',    'permission' => 'View and download', 'date' => '2026-11-15', 'reason' => 'Cross-checking figures for the procurement schedule.',                       'status' => 'pending'],
    ['id' => 5, 'user' => 'Priya Patel',     'email' => 'priya.patel@cvsu.edu.ph',   'document' => 'Board resolution No. 45.docx',    'permission' => 'View only',         'date' => '2026-11-12', 'reason' => 'Citing the resolution in a policy memo.',                                 'status' => 'approved'],
    ['id' => 6, 'user' => 'Hannah Lee',      'email' => 'hannah.lee@cvsu.edu.ph',    'document' => 'Research grants summary.pdf',     'permission' => 'View and download', 'date' => '2026-11-10', 'reason' => 'Collaborating on a joint research proposal.',                             'status' => 'approved'],
    ['id' => 7, 'user' => 'Carlos Mendoza',  'email' => 'carlos.mendoza@cvsu.edu.ph','document' => 'Faculty evaluation form.docx',    'permission' => 'View and download', 'date' => '2026-11-08', 'reason' => 'Wants a copy for personal records.',                                      'status' => 'rejected'],
];
