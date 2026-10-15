<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfMaker
{
    /** Lokasi font Roboto: writable/fonts/Roboto-Regular.ttf, Roboto-Bold.ttf, Roboto-Italic.ttf */
    public static function fontSource(): string
    {
        return WRITEPATH . 'fonts';
    }

    public static function stream(string $html, string $filename, string $paper = 'A4', string $orient = 'portrait')
    {
        $cache = WRITEPATH . 'dompdf-fonts';   // cache metrik font hasil @font-face (.ufm / .ttf)
        $tmp = WRITEPATH . 'dompdf-tmp';
        foreach ([$cache, $tmp] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }

        // Roboto jadi font bawaan bila file regular-nya ada; jika tidak, DejaVu Sans (mendukung Unicode)
        $default = is_file(self::fontSource() . '/Roboto-Regular.ttf') ? 'Roboto' : 'DejaVu Sans';

        $o = new Options();
        $o->set('defaultFont', $default);
        $o->set('fontDir', $cache);
        $o->set('fontCache', $cache);
        $o->set('tempDir', $tmp);
        $o->set('chroot', realpath(WRITEPATH));      // izinkan memuat font dari writable/fonts
        $o->set('isFontSubsettingEnabled', true);    // PDF lebih kecil: hanya glyph terpakai yang disematkan
        $o->set('isRemoteEnabled', false);           // logo/foto disisipkan sebagai data-URI
        $o->set('dpi', 96);

        $pdf = new Dompdf($o);
        $pdf->loadHtml($html);
        $pdf->setPaper($paper, $orient);
        $pdf->render();

        return service('response')
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($pdf->output());
    }
}
