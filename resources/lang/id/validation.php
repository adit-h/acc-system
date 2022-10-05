<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus tanggal setelah :date.',
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus diantara :min dan :max.',
        'file' => ':attribute harus diantara :min dan :max kilobyte.',
        'string' => ':attribute harus diantara :min dan :max karakter.',
        'array' => ':attribute harus diantara :min and :max item.',
    ],
    'boolean' => ':attribute field harus benar atau salah.',
    'confirmed' => ':attribute konfirmasi tidak sesuai.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus tanggal yg sama dengan :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus diantara :min dan :max digit.',
    'dimensions' => ':attribute dimensi gambar tidak valid.',
    'distinct' => ':attribute field memiliki value duplikat.',
    'email' => ':attribute harus alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu value: :values.',
    'exists' => 'Pilihan :attribute tidak valid.',
    'file' => ':attribute herus berupa file.',
    'filled' => ':attribute field harus berisi value.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file' => ':attribute harus lebih besar dari :value kilobyte.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
        'array' => ':attribute harus memiliki lebih dari :value item.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'file' => ':attribute harus lebih besar atau sama dengan :value kilobyte.',
        'string' => ':attribute harus lebih besar atau sama dengan :value karakter.',
        'array' => ':attribute harus memiliki :value item atau lebih.',
    ],
    'image' => ':attribute herus berupa gambar.',
    'in' => 'Pilihan :attribute tidak valid.',
    'in_array' => ':attribute field tidak ada dalam :other.',
    'integer' => ':attribute  harus berupa integer.',
    'ip' => ':attribute harus alamat IP yang valid.',
    'ipv4' => ':attribute harus alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'lt' => [
        'numeric' => ':attribute harus lebih kecil dari :value.',
        'file' => ':attribute harus lebih kecil dari :value kilobyte.',
        'string' => ':attribute harus lebih kecil dari :value karakter.',
        'array' => ':attribute harus lebih kecil dari :value item.',
    ],
    'lte' => [
        'numeric' => ':attribute harus lebih kecil atau sama dengan :value.',
        'file' => ':attribute harus lebih kecil atau sama dengan :value kilobyte.',
        'string' => ':attribute harus lebih kecil atau sama dengan :value karakter.',
        'array' => ':attribute tidak boleh lebih dari :value item.',
    ],
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'array' => ':attribute tidak boleh lebih dari :max item.',
    ],
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => ':attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute harus memiliki paling sedikit :min.',
        'file' => ':attribute harus memiliki paling sedikit :min kilobyte.',
        'string' => ':attribute harus memiliki paling sedikit :min karakter.',
        'array' => ':attribute harus memiliki paling sedikit :min item.',
    ],
    'multiple_of' => ':attribute harus kelipatan :value.',
    'not_in' => 'Pilihan :attribute tidak valid.',
    'not_regex' => 'format :attribute tidak valid.',
    'numeric' => ':attribute harus angka.',
    'password' => 'password tidak sesuai.',
    'present' => ':attribute field harus ada.',
    'regex' => 'format :attribute tidak valid.',
    'required' => ':attribute field diperlukan.',
    'required_if' => ':attribute field diperlukan bila :other adalah :value.',
    'required_unless' => ':attribute field diperlukan kecuali :other ada dalam :values.',
    'required_with' => ':attribute field diperlukan bila :values ada.',
    'required_with_all' => ':attribute field diperlukan bila :values ada.',
    'required_without' => ':attribute field diperlukan bila :values tidak ada.',
    'required_without_all' => ':attribute field diperlukan bila tidak ada :values.',
    'same' => ':attribute dan :other harus sesuai.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobyte.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus beerisi :size item.',
    ],
    'starts_with' => ':attribute harus diawali dengan value: :values.',
    'string' => ':attribute harus string.',
    'timezone' => ':attribute harus zona yang valid.',
    'unique' => ':attribute sudah ada dalam database.',
    'uploaded' => ':attribute gagal untuk upload.',
    'url' => ':attribute format tidak valid.',
    'uuid' => ':attribute harus UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
