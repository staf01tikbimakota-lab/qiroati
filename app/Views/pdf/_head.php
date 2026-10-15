<?php helper('app'); ?>
<meta charset="utf-8">
<style>
  <?= pdf_font_face() ?>
  @page { margin: 28px 32px; }
  body { font-family: 'Roboto', 'DejaVu Sans', sans-serif; font-size: 10px; color: #111; }
  table.t { width: 100%; border-collapse: collapse; }
  table.t th, table.t td { border: 1px solid #444; padding: 3px 5px; }
  table.t th { background: #e6e6e6; }
  .c { text-align: center; } .r { text-align: right; }
  h3, h4, p { margin: 2px 0; }
  .kop { width: 100%; border-bottom: 2px solid #000; margin-bottom: 8px; }
  .kop td { vertical-align: middle; padding: 0 0 5px 0; }
  .kop h3 { font-size: 14px; }
  .kop img { height: 62px; max-width: 76px; }
  .ttd { margin-top: 24px; width: 100%; } .ttd td { width: 50%; text-align: center; vertical-align: top; }
</style>
