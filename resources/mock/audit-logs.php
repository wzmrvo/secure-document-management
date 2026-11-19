<?php

/* MOCK DATA (frontend phase only). Each row: [user, action, document ('—' if none), datetime 'Y-m-d H:i', ip] */
return array_map(
    fn ($row) => ['user' => $row[0], 'action' => $row[1], 'document' => $row[2], 'datetime' => $row[3], 'ip' => $row[4]],
    [
        ['Alex Morgan',     'Access approved',     'Research ethics protocol.pdf',  '2026-11-19 09:12', '192.168.1.14'],
        ['Sarah Chen',      'Document uploaded',   'Faculty handbook 2026.pdf',     '2026-11-18 16:40', '192.168.1.32'],
        ['Dr. Emily Davis', 'Access requested',    'Q4 financial report.xlsx',      '2026-11-18 15:05', '192.168.1.57'],
        ['James Wilson',    'Document downloaded', 'IT security policy.pdf',        '2026-11-18 11:22', '192.168.1.41'],
        ['Alex Morgan',     'User added',          '—',                             '2026-11-17 14:10', '192.168.1.14'],
        ['Maria Santos',    'Document viewed',     'Campus safety guidelines.pdf',  '2026-11-17 10:03', '192.168.1.26'],
        ['Alex Morgan',     'Document edited',     'IT security policy.pdf',        '2026-11-17 09:30', '192.168.1.14'],
        ['Daniel Kim',      'Access requested',    'Faculty handbook 2026.pdf',     '2026-11-16 17:45', '192.168.1.63'],
        ['Alex Morgan',     'Access rejected',     'Faculty evaluation form.docx',  '2026-11-16 13:20', '192.168.1.14'],
        ['James Wilson',    'Document uploaded',   'Q4 financial report.xlsx',      '2026-11-16 09:55', '192.168.1.41'],
        ['Olivia Taylor',   'Access requested',    'Student enrollment report.xlsx','2026-11-15 16:12', '192.168.1.72'],
        ['Sarah Chen',      'Document viewed',     'Scholarship guidelines.pdf',    '2026-11-15 11:08', '192.168.1.32'],
        ['Alex Morgan',     'Settings changed',    '—',                             '2026-11-14 15:30', '192.168.1.14'],
        ['Dr. Emily Davis', 'Document uploaded',   'Research ethics protocol.pdf',  '2026-11-14 10:26', '192.168.1.57'],
        ['Michael Brown',   'Access requested',    'Budget proposal FY 2027.xlsx',  '2026-11-13 14:49', '192.168.1.88'],
        ['Maria Santos',    'Document uploaded',   'Staff onboarding guide.pdf',    '2026-11-12 09:14', '192.168.1.26'],
        ['Alex Morgan',     'Document deleted',    'Draft policy v0.pdf',           '2026-11-11 17:02', '192.168.1.14'],
        ['Priya Patel',     'Document downloaded', 'Procurement procedures.docx',   '2026-11-10 13:37', '192.168.1.95'],
        ['Alex Morgan',     'Login',               '—',                             '2026-11-09 08:55', '192.168.1.14'],
        ['Sarah Chen',      'Document edited',     'Faculty evaluation form.docx',  '2026-11-05 10:44', '192.168.1.32'],
        ['James Wilson',    'Document viewed',     'Budget proposal FY 2027.xlsx',  '2026-10-20 15:18', '192.168.1.41'],
        ['Alex Morgan',     'User added',          '—',                             '2026-10-12 11:02', '192.168.1.14'],
        ['Maria Santos',    'Document uploaded',   'Employee leave policy.pdf',     '2026-10-02 09:41', '192.168.1.26'],
        ['Alex Morgan',     'Login',               '—',                             '2026-09-28 08:47', '192.168.1.14'],
    ]
);
