<?php

namespace Travis;

class ArcGIS
{
    public static function geocode($address, $timeout = 30)
    {
        // submit request
        return static::request('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=pjson&SingleLine='.urlencode($address), $timeout);
    }

    public static function query($endpoint, $longitude, $latitude, $timeout = 30)
    {
        // amend endpoint
        $endpoint .= 'query?f=pjson&geometry='.urlencode($longitude.','.$latitude).'&geometryType=esriGeometryPoint&inSR=4326';

        // submit request
        return static::request($endpoint, $timeout);
    }

    protected static function request($url, $timeout)
    {
        // set headers
        $headers = [
            'Content-Type: application/json',
        ];

        // make curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // catch errors...
        if (curl_errno($ch))
        {
            #$errors = curl_error($ch);

            throw new \Exception('Unknown error.');
        }

        // else if NO errors...
        else
        {
            // decode
            $result = json_decode($response);
        }

        // close
        curl_close($ch);

        // catch error...
        if (!$result) throw new \Exception('Invalid reponse.');

        // catch error...
        if ($httpcode >= 400)
        {
            throw new \Exception(ex($result, 'error.message'));
        }

        // return
        return $result;
    }
}