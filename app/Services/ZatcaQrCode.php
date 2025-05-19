<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ZatcaQrCode
{
    public static function generate(\App\Models\Invoice $invoice)
    {
        try {
            // 1. تحضير بيانات البائع
            $sellerName = config('app.name');
            $vatNumber = '300000000000003'; // يجب استبدالها برقم السجل الضريبي الفعلي
            
            // 2. تحضير بيانات الفاتورة
            $invoiceDate = $invoice->created_at->format('Y-m-d\TH:i:s\Z');
            $totalAmount = number_format($invoice->total, 2, '.', '');
            $taxAmount = number_format($invoice->tax, 2, '.', '');
            
            // 3. إنشاء مصفوفة البيانات وفق معايير ZATCA
            $tlvData = [
                // اسم البائع (1)
                self::toTlv('1', $sellerName),
                // الرقم الضريبي للبائع (2)
                self::toTlv('2', $vatNumber),
                // تاريخ ووقت الفاتورة (3)
                self::toTlv('3', $invoiceDate),
                // إجمالي الفاتورة مع الضريبة (4)
                self::toTlv('4', $totalAmount),
                // إجمالي الضريبة (5)
                self::toTlv('5', $taxAmount)
            ];
            
            // 4. دمج بيانات TLV
            $tlvString = implode('', $tlvData);
            
            // 5. إنشاء Base64 من البيانات
            $binaryData = '';
            foreach (str_split($tlvString, 2) as $hexPair) {
                $binaryData .= chr(hexdec($hexPair));
            }
            
            $base64Data = base64_encode($binaryData);
            
            // 7. إنشاء QR Code
            $qrCode = QrCode::format('svg')
                ->size(200)
                ->generate($base64Data);
                
            return [
                'qr_code' => $qrCode,
                'tlv_data' => $tlvString,
                'base64' => $base64Data
            ];
            
        } catch (\Exception $e) {
            // إرجاع QR Code افتراضي في حالة الخطأ
            $defaultQr = QrCode::format('svg')
                ->size(200)
                ->generate('Error generating QR Code');
                
            return [
                'qr_code' => $defaultQr,
                'error' => $e->getMessage(),
                'tlv_data' => '',
                'base64' => ''
            ];
        }
    }
    
    private static function toTlv($tag, $value)
    {
        $value = (string)$value;
        
        // تحويل القيمة إلى بايتات
        $valueBytes = [];
        for ($i = 0; $i < strlen($value); $i++) {
            $valueBytes[] = ord($value[$i]);
        }
        
        // إنشاء بايتات الطول (1 بايت كحد أقصى للطول)
        $length = count($valueBytes);
        if ($length > 255) {
            throw new \Exception('القيمة طويلة جداً للتحويل إلى TLV');
        }
        
        // إنشاء مصفوفة البايتات النهائية
        $result = [
            hexdec($tag), // بايت العلامة
            $length       // بايت الطول
        ];
        
        // إضافة بايتات القيمة
        $result = array_merge($result, $valueBytes);
        
        // تحويل المصفوفة إلى سلسلة سداسية عشرية
        $hexString = '';
        foreach ($result as $byte) {
            $hexString .= str_pad(dechex($byte), 2, '0', STR_PAD_LEFT);
        }
        
        return $hexString;
    }
}
