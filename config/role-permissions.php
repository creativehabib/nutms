<?php

return [
    'permissions' => [
        'colleges.view' => 'View college lists and profiles',
        'colleges.create' => 'Create new colleges',
        'colleges.update' => 'Edit college profiles',
        'colleges.approve' => 'Approve colleges',
        'teachers.view' => 'View teacher lists and profiles',
        'teachers.create' => 'Create teacher profiles',
        'teachers.update' => 'Edit teacher profiles',
        'teachers.approve' => 'Approve teachers',
        'teachers.delete' => 'Delete and restore teachers',
        'teachers.assign-role' => 'Change teacher roles',
        'reference-data.manage' => 'Manage reference data',
        'training-catalog.manage' => 'Manage training catalog',
        'reports.view' => 'View all reports',
        'roles.manage' => 'Manage roles and permissions',
    ],
    'defaults' => [
        'admin' => ['*'],
        'principal' => ['colleges.view', 'colleges.update', 'teachers.view', 'teachers.create', 'teachers.update', 'teachers.approve'],
        'teacher' => ['teachers.create', 'teachers.view', 'teachers.update'],
    ],
];
