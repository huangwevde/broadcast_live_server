<?php

$env = getenv('APPLICATION_ENV');

/**
 * 开发环境支付配置
 */
if ($env == 'dev') {
    return [
        'im_tls' => [
            // 阿里公共密钥，验证签名时使用
            'public_key' => '-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEfi/jPesz4wkxbhyldeOwqnjIUgf/
Il+QcOeAh6NUqTrFpYOXmn0y/5MDZHiYDhkB7HFkFdDN6H+Y5AUdqKSmQQ==
-----END PUBLIC KEY-----',

            // 自己的私钥，签名时使用
            'private_key' => '-----BEGIN PRIVATE KEY-----
MIGHAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBG0wawIBAQQgUBwTjYJOfA+BtHEI
xfAmcc75JOa38kDYFcMMwE5awB2hRANCAAR+L+M96zPjCTFuHKV147CqeMhSB/8i
X5Bw54CHo1SpOsWlg5eafTL/kwNkeJgOGQHscWQV0M3of5jkBR2opKZB
-----END PRIVATE KEY-----',
        ],
    ];
}
