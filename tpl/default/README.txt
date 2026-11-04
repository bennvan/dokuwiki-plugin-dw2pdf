====== dw2pdf Templates ======

Templates define the design of the created PDF files and are a good way
to easily customize them to your Corporate Identity.

To create a persistent user template, create ''conf/dw2pdf/tpl/mytemplate/''
(relative to DokuWiki's configuration directory) and put your headers, footers
and style definitions in it. The plugin's own ''tpl'' folder remains supported.
User templates take precedence over bundled templates with the same name;
this replaces the whole template, without inheriting missing files.

Both locations appear in the configuration's template selector. To select a
template for a single export, add ''&tpl=mytemplate'' to the export URL.
Template names may contain letters, numbers, underscores, spaces, dots and
hyphens, but must start with a letter, number or underscore.

Use ''@TPLBASE@logo.png'' or ''@TPLINC@logo.png'' for images in template HTML.
For user templates, these resolve to local files; do not expose ''conf'' over
HTTP. Relative ''url(images/logo.png)'' paths in ''style.css'' resolve locally
too. Template files are data, not executable PHP.

Changes to template HTML, CSS and local assets invalidate the PDF cache on the
next export. Adding or removing a user override also selects a separate cache.

===== Custom fonts =====

Put static TrueType (''.ttf'') files inside your template, for example in a
''fonts'' subdirectory. Web fonts (WOFF/WOFF2) and variable fonts must first be
converted to static TrueType files. Only bundle fonts whose licences permit it.

Register them in ''fonts.json'' alongside ''style.css'':

<code json>
{
  "inter": {
    "R": "fonts/Inter-Regular.ttf",
    "B": "fonts/Inter-Bold.ttf",
    "I": "fonts/Inter-Italic.ttf",
    "BI": "fonts/Inter-BoldItalic.ttf"
  },
  "sourcecodepro": {
    "R": "fonts/SourceCodePro-Regular.ttf"
  }
}
</code>

Family names must be lowercase, start with a letter, and contain only letters,
numbers, underscores or hyphens. ''R'' (regular) is required; ''B'' (bold), ''I''
(italic) and ''BI'' (bold italic) are optional. Paths are relative to this
template and must resolve to files within it. Missing or invalid font files
fail the export and are reported in the error log rather than silently falling
back to another font. Existing mPDF fonts remain available.

Then choose fonts in ''style.css'':

<code css>
body { font-family: inter; }
pre, code { font-family: sourcecodepro; }
</code>

Registration does not change the default font by itself. A PDF template can
choose fonts independently of the browser theme; the browser's ''@font-face''
rules are not imported. Font metrics are cached separately for each template
and font revision. Updating local fonts also invalidates the PDF cache.

===== Headers and Footers =====

The following files can be created and will be used to set headers and
footers on odd or even pages. Special headers/footers can be used on the
first page of a document. If a file is does not exist the next more generic
one will be tried. Eg. if You don't differ between even and odd pages,
just the header.html is used.

  * ''header.html'' -- Header for all pages
  * ''header_odd.html'' -- Header for odd pages
  * ''header_even.html'' -- Header for even pages
  * ''header_first.html'' -- Header for the first page

  * ''footer.html'' -- Footer for all pages
  * ''footer_odd.html'' -- Footer for odd pages
  * ''footer_even.html'' -- Footer for even pages
  * ''footer_first.html'' -- Footer for the first page

  * ''citation.html'' -- Citationbox to be printed after each article
  * ''cover.html'' -- Added once before first page
  * ''back.html'' -- Added once after last page

You can use all HTML that is understood by mpdf
(See http://mpdf1.com/manual/index.php?tid=256)

If you reference image files, be sure to prefix them with the @TPLBASE@
parameter (See [[#Replacements]] below).

===== Replacements =====

The following replacement patterns can be used within the header and
footer files.

  * ''@PAGE@'' -- current page number in the PDF
  * ''@PAGES@'' -- number of all pages in the PDF
  * ''@TITLE@'' -- The article's title
  * ''@WIKI@'' -- The wiki's title
  * ''@WIKIURL@'' -- URL to the wiki
  * ''@DATE@'' -- time when the PDF was created (might be in the past if cached)
  * ''@BASE@'' -- the wiki base directory
  * ''@INC@'' -- the absolute wiki install directory on the filesystem
  * ''@TPLBASE@'' -- the PDF template base directory (use to reference images)
  * ''@TPLINC@'' -- the absolute path to the PDF template directory on the filesystem

//Remark about Bookcreator//:
The page depended replacements are only for ''citation.html'' updated for every page.
In the headers and footers the ID of the bookmanager page of the Bookcreator is applied.
  * ''@ID@'' -- The article's pageID
  * ''@PAGEURL@'' -- URL to the article
  * ''@UPDATE@'' -- Time of the last update of the article
  * ''@QRCODE@'' -- QR code image pointing to the original page url (requires an online generator, see config setting)

===== Styles =====

Custom stylings can be provided in the following file of your dw2pdf-template folder:

  * style.css

You can use all the CSS that is understood by mpdf
(See http://mpdf1.com/manual/index.php?tid=34)

