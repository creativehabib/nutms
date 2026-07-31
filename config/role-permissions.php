<?php

return [
    'permissions' => [
        'colleges.view' => 'কলেজের তালিকা ও প্রোফাইল দেখা',
        'colleges.create' => 'নতুন কলেজ তৈরি',
        'colleges.update' => 'কলেজ প্রোফাইল সম্পাদনা',
        'colleges.approve' => 'কলেজ অনুমোদন',
        'teachers.view' => 'শিক্ষকের তালিকা ও প্রোফাইল দেখা',
        'teachers.create' => 'শিক্ষক প্রোফাইল তৈরি',
        'teachers.update' => 'শিক্ষক প্রোফাইল সম্পাদনা',
        'teachers.approve' => 'শিক্ষক অনুমোদন',
        'teachers.delete' => 'শিক্ষক মুছে ফেলা ও পুনরুদ্ধার',
        'teachers.assign-role' => 'শিক্ষকের রোল পরিবর্তন',
        'reference-data.manage' => 'রেফারেন্স ডাটা ব্যবস্থাপনা',
        'training-catalog.manage' => 'ট্রেনিং ক্যাটালগ ব্যবস্থাপনা',
        'reports.view' => 'সকল রিপোর্ট দেখা',
        'roles.manage' => 'রোল ও পারমিশন ব্যবস্থাপনা',
    ],
    'defaults' => [
        'admin' => ['*'],
        'principal' => ['colleges.view', 'colleges.update', 'teachers.view', 'teachers.create', 'teachers.update', 'teachers.approve'],
        'teacher' => ['teachers.create', 'teachers.view', 'teachers.update'],
    ],
];
