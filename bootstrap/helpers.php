<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

if (!function_exists('tcp_data_encode')) {
    function tcp_data_encode(string $token, int $command, array $data)
    {
        $data_encoded = '010000000000';
        // $data_encoded = '';
        // VERSION [Default 01]
        /*if (isset($data['VERSION']) && in_array($data['VERSION'], [0, 1])) {
            $data_encoded = str_pad(dechex($data['VERSION']), 2, '0', STR_PAD_LEFT);
        } else {
            $data_encoded = '01';
        }*/
        // ENCRYPT [Default 00]
        /*if (isset($data['ENCRYPT']) && in_array($data['ENCRYPT'], [0, 1])) {
            $data_encoded .= str_pad(dechex($data['ENCRYPT']), 2, '0', STR_PAD_LEFT);
        } else {
            $data_encoded .= '00';
        }*/
        // STATUS [Default 00000000]
        /*if (isset($data['STATUS']) && in_array($data['STATUS'], [0, 1])) {
            $data_encoded .= str_pad(dechex($data['STATUS']), 8, '0', STR_PAD_LEFT);
        } else {
            $data_encoded .= '00000000';
        }*/
        // TOKEN
        /*if (isset($data['TOKEN'])) {
            $token = bin2hex($data['TOKEN']);
            if (strlen($token) == 64) {
                $data_encoded .= strtoupper($token);
            } else {
                return false;
            }
        } else {
            return false;
        }*/
        $token = bin2hex($token);
        if (strlen($token) == 64) {
            $data_encoded .= strtoupper($token);
        } else {
            return false;
        }
        // COMMAND
        /*if (isset($data['COMMAND'])) {
            $data_encoded .= str_pad(strtoupper(dechex($data['COMMAND'])), 8, '0', STR_PAD_LEFT);
        } else {
            return false;
        }*/
        $data_encoded .= str_pad(strtoupper(dechex($command)), 8, '0', STR_PAD_LEFT);
        // DATA
        $keys_values_length = 0;
        $keys_values_encoded = '';
        /*if (isset($data['DATA']) && is_array($data['DATA'])) {
            foreach ($data['DATA'] as $key => $value) {
                $key_encoded = strtoupper(bin2hex($key));
                $value_encoded = strtoupper(bin2hex($value));
                $key_length = (strlen($key_encoded)) / 2;
                $value_length = (strlen($value_encoded)) / 2;
                $keys_values_length += 8 + $key_length + $value_length;
                $keys_values_encoded .= str_pad(strtoupper(dechex($key_length)), 8, '0', STR_PAD_LEFT) . $key_encoded . str_pad(strtoupper(dechex($value_length)), 8, '0', STR_PAD_LEFT) . $value_encoded;
            }
        } else {
            return false;
        }*/
        foreach ($data as $key => $value) {
            $key_encoded = strtoupper(bin2hex($key));
            $value_encoded = strtoupper(bin2hex($value));
            $key_length = (strlen($key_encoded)) / 2;
            $value_length = (strlen($value_encoded)) / 2;
            $keys_values_length += 8 + $key_length + $value_length;
            $keys_values_encoded .= str_pad(strtoupper(dechex($key_length)), 8, '0', STR_PAD_LEFT) . $key_encoded . str_pad(strtoupper(dechex($value_length)), 8, '0', STR_PAD_LEFT) . $value_encoded;
        }
        // LENGTH
        /*if (isset($data['LENGTH'])) {
            $data_encoded .= str_pad(strtoupper(dechex($data['LENGTH'])), 8, '0', STR_PAD_LEFT);
        } else {
            $data_encoded .= str_pad(strtoupper(dechex($keys_values_length)), 8, '0', STR_PAD_LEFT);
        }*/
        $data_encoded .= str_pad(strtoupper(dechex($keys_values_length)), 8, '0', STR_PAD_LEFT);
        // DATA
        $data_encoded .= $keys_values_encoded;
        // END [协议分隔符 ##_**]
        $data_encoded .= '23235F2A2A'; // 协议分隔符 ##_**

        return $data_encoded;
    }
}

if (!function_exists('tcp_data_decode')) {
    function tcp_data_decode(string $data)
    {
        if (!is_string($data) || strlen($data) < 102) {
            return false;
        }
        $data_decoded = [];
        // VERSION
        $data_decoded['VERSION'] = hexdec(ltrim(substr($data, 0, 2), '0'));
        // ENCRYPT
        $data_decoded['ENCRYPT'] = hexdec(ltrim(substr($data, 2, 2), '0'));
        // STATUS
        if (substr($data, 4, 8) === '00000000') {
            $data_decoded['STATUS'] = 0;
        } else {
            $data_decoded['STATUS'] = hexdec(ltrim(substr($data, 4, 8), '0'));
        }
        // TOKEN
        $data_decoded['TOKEN'] = hex2bin(substr($data, 12, 64));
        // COMMAND
        $data_decoded['COMMAND'] = hexdec(ltrim(substr($data, 76, 8), '0'));
        // DATA
        $data_decoded['DATA'] = [];
        // LENGTH
        if (substr($data, 84, 8) === '00000000') {
            $data_decoded['STATUS'] = 0;
        } else {
            $data_decoded['LENGTH'] = hexdec(ltrim(substr($data, 84, 8), '0'));
            $keys_values = substr($data, 92, -10);
            if (strlen($keys_values) != ($data_decoded['LENGTH'] * 2) || $data_decoded['LENGTH'] < 8) {
                return false;
            }
            while ($keys_values != '') {
                if (strlen($keys_values) < 16) {
                    return false;
                }
                $key_length = hexdec(ltrim(substr($keys_values, 0, 8), '0')) * 2;
                $key = substr($keys_values, 8, $key_length);
                $length = 8 + strlen($key);
                $key = hex2bin($key);
                $keys_values = substr($keys_values, $length);
                if (strlen($keys_values) < 8) {
                    return false;
                }
                $value_length = hexdec(ltrim(substr($keys_values, 0, 8), '0')) * 2;
                $value = substr($keys_values, 8, $value_length);
                $length = 8 + strlen($value);
                $value = hex2bin($value);
                $data_decoded['DATA'][$key] = $value;
                $keys_values = substr($keys_values, $length);
            }
        }

        return $data_decoded;
    }
}
