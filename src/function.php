<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:19
 */

if (!function_exists('douyin_mini_program_decrypt')) {
    /**
     * 解密抖音小程序数据
     * @param string $encrypted_data Base64编码的加密数据
     * @param string $session_key Base64编码的会话密钥
     * @param string $iv Base64编码的初始向量
     * @return bool|string 解密后的数据，失败时返回false
     */
    function douyin_mini_program_decrypt(string $encrypted_data, string $session_key, string $iv): bool|string
    {
        $data = base64_decode($encrypted_data);
        $key = base64_decode($session_key);
        $iv = base64_decode($iv);

        $decrypted_data = openssl_decrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted_data === false) {
            return false;
        }

        return $decrypted_data;
    }
}