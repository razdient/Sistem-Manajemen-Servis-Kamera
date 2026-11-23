<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi
    |--------------------------------------------------------------------------
    |
    | Pesan berikut digunakan oleh fitur validasi Laravel. Hanya aturan yang
    | dipakai aplikasi ini yang diterjemahkan agar tetap mudah dirawat.
    |
    */

    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'numeric' => ':attribute harus berupa angka.',
    'boolean' => ':attribute harus bernilai ya atau tidak.',
    'array' => ':attribute harus berupa daftar.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute harus berupa tanggal yang valid.',

    'min' => [
        'array' => ':attribute minimal berisi :min item.',
        'file' => ':attribute minimal :min kilobyte.',
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],

    'max' => [
        'array' => ':attribute maksimal berisi :max item.',
        'file' => ':attribute maksimal :max kilobyte.',
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Atribut
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'nama',
        'phone' => 'nomor telepon',
        'address' => 'alamat',
        'notes' => 'catatan',
        'customer_id' => 'pelanggan',
        'devices' => 'perangkat',
        'devices.*.device_type' => 'jenis perangkat',
        'devices.*.brand' => 'merek',
        'devices.*.model' => 'model',
        'devices.*.complaint' => 'keluhan',
        'devices.*.physical_condition' => 'kondisi fisik',
        'devices.*.accessories' => 'kelengkapan',
        'device_id' => 'perangkat',
        'catalog_id' => 'katalog',
        'item_type' => 'jenis rincian',
        'description' => 'deskripsi',
        'quantity' => 'kuantitas',
        'unit_price' => 'harga jual',
        'unit_cost' => 'harga modal',
        'category' => 'kategori',
        'default_price' => 'harga jual default',
        'default_cost' => 'harga modal default',
        'is_active' => 'status aktif',
        'amount' => 'nominal',
        'payment_type' => 'jenis pembayaran',
        'payment_method' => 'metode pembayaran',
        'paid_at' => 'tanggal pembayaran',
        'expense_date' => 'tanggal pengeluaran',
    ],

];
