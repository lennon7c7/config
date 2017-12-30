<?php

/**
 * Class Model
 *
 * @property Cart\Cart cart
 * @property Cart\Tax tax
 * @property Cart\Customer customer
 * @property Cart\User user
 * @property Cart\Currency currency
 * @property Cart\Weight weight
 * @property Cache cache
 * @property Loader load
 * @property DB db
 * @property Request request
 * @property Language language
 * @property Session session
 * @property Response response
 * @property Url url
 * @property Config config
 * @property Event event
 * @property Document document
 * @property Log log
 * @property ModelUserApi model_user_api
 * @property ModelUserUser model_user_user
 * @property ModelUserUserGroup model_user_user_group
 * @property ModelSaleOrder model_sale_order
 * @property ModelSaleReturn model_sale_return
 * @property ModelSaleVoucher model_sale_voucher
 * @property ModelSaleVoucherTheme model_sale_voucher_theme
 * @property ModelLocalisationLanguage model_localisation_language
 * @property ModelLocalisationCurrency model_localisation_currency
 * @property ModelLocalisationCountry model_localisation_country
 * @property ModelLocalisationZone model_localisation_zone
 * @property ModelLocalisationOrderStatus model_localisation_order_status
 * @property ModelLocalisationLengthClass model_localisation_length_class
 * @property ModelLocalisationWeightClass model_localisation_weight_class
 * @property ModelLocalisationStockStatus model_localisation_stock_status
 * @property ModelLocalisationTaxClass model_localisation_tax_class
 * @property ModelCustomerCustomer model_customer_customer
 * @property ModelCustomerCustomerGroup model_customer_customer_group
 * @property ModelCustomerCustomField model_customer_custom_field
 * @property ModelSettingSetting model_setting_setting
 * @property ModelSettingStore model_setting_store
 * @property ModelCatalogCategory model_catalog_category
 * @property ModelCatalogProduct model_catalog_product
 * @property ModelCatalogManufacturer model_catalog_manufacturer
 * @property ModelCatalogOption model_catalog_option
 * @property ModelCatalogAttribute model_catalog_attribute
 * @property ModelCatalogAttributeGroup model_catalog_attribute_group
 * @property ModelCatalogReview model_catalog_review
 * @property ModelCatalogDownload model_catalog_download
 * @property ModelCatalogFilter model_catalog_filter
 * @property ModelCatalogRecurring model_catalog_recurring
 * @property ModelCatalogInformation model_catalog_information
 * @property ModelCatalogUrlAlias model_catalog_url_alias
 * @property ModelDesignLayout model_design_layout
 * @property ModelDesignBanner model_design_banner
 * @property ModelDesignLanguage model_design_language
 * @property ModelDesignMenu model_design_menu
 * @property ModelDesignTheme model_design_theme
 * @property ModelReportCustomer model_report_customer
 * @property ModelReportCustomerPurchasedProduct model_report_customer_purchased_product
 * @property ModelReportAffiliate model_report_affiliate
 * @property ModelReportMarketing model_report_marketing
 * @property ModelReportProduct model_report_product
 * @property ModelReportCoupon model_report_coupon
 * @property ModelReportSale model_report_sale
 * @property ModelReportReturn model_report_return
 * @property ModelLocalisationGeoZone model_localisation_geo_zone
 * @property ModelLocalisationTaxRate model_localisation_tax_rate
 * @property ModelLocalisationLocation model_localisation_location
 * @property ModelLocalisationReturnAction model_localisation_return_action
 * @property ModelLocalisationReturnReason model_localisation_return_reason
 * @property ModelLocalisationReturnStatus model_localisation_return_status
 * @property ModelMarketingMarketing model_marketing_marketing
 * @property ModelMarketingCoupon model_marketing_coupon
 * @property ModelMarketingAffiliate model_marketing_affiliate
 * @property ModelExtensionModule model_extension_module
 * @property ModelExtensionExtension model_extension_extension
 * @property ModelExtensionEvent model_extension_event
 * @property ModelAccountCustomer model_account_customer
 * @property ModelAccountActivity model_account_activity
 * @property ModelAccountCustomerGroup model_account_customer_group
 * @property ModelAccountCustomField model_account_custom_field
 * @property ModelAccountOrder model_account_order
 * @property ModelAccountWishlist model_account_wishlist
 * @property ModelAccountantAccountant model_accountant_accountant
 * @property ModelCheckoutOrder model_checkout_order
 * @property ModelCheckoutMarketing model_checkout_marketing
 * @property ModelToolUpload model_tool_upload
 * @property ModelToolImage model_tool_image
 * @property ModelToolExportImport model_tool_export_import
 * @property ModelAffiliateAffiliate model_affiliate_affiliate
 *
 * @property ModelReportProductQuantitySituation model_report_product_quantity_situation
 * @property ModelReportProductPurchasedSupplemented model_report_product_purchased_supplemented
 * @property ModelReportStoreProductQuantity model_report_store_product_quantity
 * @property ModelCatalogLogProduct model_catalog_log_product
 * @property ModelCatalogLogProductStatus model_catalog_log_product_status
 */
abstract class Model
{
    protected $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }
}