;<?php exit; ?>

;
;   Additional System Settings.
;   All keys must be different from config.php keys.
;

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Charset
;;;;;
STOREFRONT_CHARSET = "UTF-8"

ADMIN_ZONE_CHARSET = "UTF-8"

NOTIFICATIONS_CHARSET = "UTF-8"

TEMPLATE_CHARSET = "iso-8859-1"

IO_CHARSET = "iso-8859-1"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; System
;;;;;
; May be ENABLED or DISABLED
WRITE_LOG = DISABLED

; "PHP_INI", "AVACTIS_CACHE_DIR" or "/abs/path/to/tmp"
SESSION_SAVE_PATH = "PHP_INI"

; "PHP_INI", "DB" or "<handler>" ("files")
SESSION_SAVE_HANDLER = "PHP_INI"



;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Cache files location (Optional)
;;;;;
; PATH_CACHE_DIR -                             ,                           cache      ,
;                                                         .
;   -         ,                         [installdir]/avactis-conf/cache/
;PATH_CACHE_DIR = "/tmp/"




;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Current Language Settings
;;;;;
;REPLACE_RESOURCES = DISABLED
LANGUAGE = eng




;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; News Server Settings
;;;;;
NEWS_SERVER = http://www.avactis.com/news-gateway/


NEWS_GATEWAY = rss.php


MARKETPLACE_SERVER = http://marketplace.avactis.com/



;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; E-Goods files Location (Optional)
;;;;;
;                             ,
;                                                         .
; PRODUCT_FILES_DIR = "c:/var/_product_files/"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Bouncer Default Settings
;;;;;

;           proxy.
;                   *_HOST -> host[:port]              !
;                    proxy                  ,                  *_HOST = ""
HTTP_PROXY_HOST = ""
HTTP_PROXY_USER = ""
HTTP_PROXY_PASS = ""

HTTPS_PROXY_HOST = ""
HTTPS_PROXY_USER = ""
HTTPS_PROXY_PASS = ""

; Default HTTP Protocol version.
; May be "1.0" or "1.1"
DEFAULT_HTTP_PROTOCOL_VERSION = "1.1"

; Default socket connection and stream timeout
; In seconds
DEFAULT_TIMEOUT = 30




;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; SEFU (Search Engine Friednly URL) Settings
;;;;;
SEFU_CATEGORY_QUERY_STRING_SUFFIX = "pg{%page_number%}-cid{%category_id%}.html"
SEFU_PRODUCT_QUERY_STRING_SUFFIX = "pid{%product_id%}.html"
SEFU_CMSPAGE_QUERY_STRING_SUFFIX = "page_id={%page_id%}"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Affilite Tracking
;;;;;
;;;;; GET Parameter that will contain Affiliate ID.
;;;;; WARNING!Change it with care, so that it has not to match with any system parameters in storefront.
;;;;;
AFFILIATE_ID_PARAM = "aid"



;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Australia Post
;;;;;
AUP_HTTP_PROTOCOL_VERSION = "1.0"
AUP_SERVER_URL = "http://drc.edeliver.com.au/ratecalc.asp"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Canada Post
;;;;;
CPC_HTTP_PROTOCOL_VERSION = "1.0"
CPC_LIVE_SERVER_URL = "http://sellonline.canadapost.ca:30000/"
;;CPC_TEST_SERVER_URL = "http://206.191.4.228:30000/"
CPC_TEST_SERVER_URL = "http://sellonline.canadapost.ca:30000/"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Freight101
;;;;;
FREIGHT101_HTTP_PROTOCOL_VERSION = "1.0"
FREIGHT101_SERVER_URL = "http://api.freight101.com/freight101.php"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; InterShipper
;;;;;
INTERSHIPPER_HTTP_PROTOCOL_VERSION = "1.0"
INTERSHIPPER_SERVER_URL = "https://www.intershipper.com/Interface/Intershipper/XML/v2.0/HTTP.jsp"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; USPS
;;;;;
USPS_HTTP_PROTOCOL_VERSION = "1.0"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Authorize.Net
;;;;;
AUTHORIZE_NET_DELIM_CHAR = ","
AUTHORIZE_NET_ENCAP_CHAR = ""

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; EPay USA
;;;;;
EPAYUSA_DELIM_CHAR = ","
EPAYUSA_ENCAP_CHAR = ""

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; MerchantPlus
;;;;;
MERCHANTPLUS_DELIM_CHAR = ","
MERCHANTPLUS_ENCAP_CHAR = ""

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Main Product Images: Auto Thumbnail Generation
;;;;;
JPEG_THUMBNAIL_QUALITY = "85"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Payment Module ProtX Direct background request
;;;;; socket read timeout
;;;;;
; Default socket connection and stream timeout
; In seconds
PROTX_DIRECT_DEFAULT_TIMEOUT = 20


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;; Product List Sort Options

; PRODUCT_LIST_SORTER_LINKS - links to display
; Available values:
;       SORT_BY_PRODUCT_SORT_ORDER
;       SORT_BY_PRODUCT_SALE_PRICE
;       SORT_BY_PRODUCT_LIST_PRICE
;       SORT_BY_PRODUCT_NAME
;       SORT_BY_PRODUCT_DATE_ADDED
;       SORT_BY_PRODUCT_DATE_UPDATED
;       SORT_BY_PRODUCT_QUANTITY_IN_STOCK
;       SORT_BY_PRODUCT_SKU
;       SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST
;       SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST
;       SORT_BY_PRODUCT_WEIGHT
PRODUCT_LIST_SORTER_LINKS = "SORT_BY_PRODUCT_SALE_PRICE,SORT_BY_PRODUCT_NAME, SORT_BY_PRODUCT_WEIGHT, SORT_BY_PRODUCT_SORT_ORDER"

PRODUCT_LIST_SORTER_DEFAULT = "SORT_BY_PRODUCT_SORT_ORDER"


;UPLOAD_FILES_DIR = "c:/var/_uploads/"


PRODUCT_LIST_DISABLE_TR_TD = "yes"