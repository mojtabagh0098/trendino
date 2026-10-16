# Translation files

Trendino loads its text domain from this folder (`load_theme_textdomain( 'trendino', get_template_directory() . '/languages' )`).

Generate the POT file with WP-CLI:

```
wp i18n make-pot . languages/trendino.pot --domain=trendino
```

Then add `trendino-fa_IR.po` / `.mo` (and any other locale) here.
