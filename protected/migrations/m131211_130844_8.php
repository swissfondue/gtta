<?php

/**
 * Migration m131211_130844_8
 */
class m131211_130844_8 extends CDbMigration {
    /**
     * Up migration
     * @return bool
     */
    public function safeUp() {
        $packageData = array(
            // libraries
            array("core", Package::TYPE_LIBRARY),
            array("cms_explorer", Package::TYPE_LIBRARY),
            array("havester", Package::TYPE_LIBRARY),
            array("smtp", Package::TYPE_LIBRARY),
            array("spf", Package::TYPE_LIBRARY),
            array("sqid", Package::TYPE_LIBRARY),
            array("sslyze", Package::TYPE_LIBRARY),
            array("vulndetector", Package::TYPE_LIBRARY),
            array("w3af", Package::TYPE_LIBRARY),

            // scripts
            array("apache_dos", Package::TYPE_SCRIPT),
            array("asn_info", Package::TYPE_SCRIPT),
            array("auth_detection", Package::TYPE_SCRIPT),
            array("call_by_ip", Package::TYPE_SCRIPT),
            array("check_as_peers", Package::TYPE_SCRIPT),
            array("check_ssh_version", Package::TYPE_SCRIPT),
            array("cms_detection", Package::TYPE_SCRIPT),
            array("dns_a", Package::TYPE_SCRIPT),
            array("dns_a_nr", Package::TYPE_SCRIPT),
            array("dns_afxr", Package::TYPE_SCRIPT),
            array("dns_b_nr", Package::TYPE_SCRIPT),
            array("dns_dom_mx", Package::TYPE_SCRIPT),
            array("dns_find_ns", Package::TYPE_SCRIPT),
            array("dns_hosting", Package::TYPE_SCRIPT),
            array("dns_reverse_lookup", Package::TYPE_SCRIPT),
            array("dns_soa", Package::TYPE_SCRIPT),
            array("dns_spf", Package::TYPE_SCRIPT),
            array("dns_top_tlds", Package::TYPE_SCRIPT),
            array("doc_files_crawler", Package::TYPE_SCRIPT),
            array("ftp_bruteforce", Package::TYPE_SCRIPT),
            array("fuzz_check", Package::TYPE_SCRIPT),
            array("google_search_email", Package::TYPE_SCRIPT),
            array("grep_url", Package::TYPE_SCRIPT),
            array("http_banner", Package::TYPE_SCRIPT),
            array("http_dos", Package::TYPE_SCRIPT),
            array("icmp_ip_id", Package::TYPE_SCRIPT),
            array("joomla_scan", Package::TYPE_SCRIPT),
            array("linked_partners", Package::TYPE_SCRIPT),
            array("login_pages", Package::TYPE_SCRIPT),
            array("nic_typosquatting", Package::TYPE_SCRIPT),
            array("nic_whois", Package::TYPE_SCRIPT),
            array("nikto", Package::TYPE_SCRIPT),
            array("nmap_tcp", Package::TYPE_SCRIPT),
            array("nmap_tcp_os", Package::TYPE_SCRIPT),
            array("nmap_udp", Package::TYPE_SCRIPT),
            array("ns_version", Package::TYPE_SCRIPT),
            array("params_crawler", Package::TYPE_SCRIPT),
            array("ping", Package::TYPE_SCRIPT),
            array("redirects", Package::TYPE_SCRIPT),
            array("renegotiation", Package::TYPE_SCRIPT),
            array("smtp_auth", Package::TYPE_SCRIPT),
            array("smtp_banner", Package::TYPE_SCRIPT),
            array("smtp_dnsbl", Package::TYPE_SCRIPT),
            array("smtp_filter", Package::TYPE_SCRIPT),
            array("smtp_relay", Package::TYPE_SCRIPT),
            array("smtp_starttls", Package::TYPE_SCRIPT),
            array("smtp_user_verification", Package::TYPE_SCRIPT),
            array("snmp_community", Package::TYPE_SCRIPT),
            array("sql_injector", Package::TYPE_SCRIPT),
            array("ssh_bruteforce", Package::TYPE_SCRIPT),
            array("ssl_cert_usage", Package::TYPE_SCRIPT),
            array("ssl_certificate", Package::TYPE_SCRIPT),
            array("ssl_ciphers", Package::TYPE_SCRIPT),
            array("ssl_key_size", Package::TYPE_SCRIPT),
            array("ssl_quality", Package::TYPE_SCRIPT),
            array("ssl_test", Package::TYPE_SCRIPT),
            array("ssl_validity", Package::TYPE_SCRIPT),
            array("subdomain_bruteforce", Package::TYPE_SCRIPT),
            array("tcp_timestamp", Package::TYPE_SCRIPT),
            array("tcp_traceroute", Package::TYPE_SCRIPT),
            array("theharvester_emails", Package::TYPE_SCRIPT),
            array("udp_traceroute", Package::TYPE_SCRIPT),
            array("urlscan", Package::TYPE_SCRIPT),
            array("user_dirs_access", Package::TYPE_SCRIPT),
            array("w3af_ajax", Package::TYPE_SCRIPT),
            array("w3af_bing_spider", Package::TYPE_SCRIPT),
            array("w3af_blank_body", Package::TYPE_SCRIPT),
            array("w3af_code_disclosure", Package::TYPE_SCRIPT),
            array("w3af_collect_cookies", Package::TYPE_SCRIPT),
            array("w3af_detect_reverse_proxy", Package::TYPE_SCRIPT),
            array("w3af_detect_transparent_proxy", Package::TYPE_SCRIPT),
            array("w3af_directory_indexing", Package::TYPE_SCRIPT),
            array("w3af_dom_xss", Package::TYPE_SCRIPT),
            array("w3af_domain_dot", Package::TYPE_SCRIPT),
            array("w3af_dot_net_errors", Package::TYPE_SCRIPT),
            array("w3af_dot_net_event_validation", Package::TYPE_SCRIPT),
            array("w3af_favicon_identification", Package::TYPE_SCRIPT),
            array("w3af_feeds", Package::TYPE_SCRIPT),
            array("w3af_file_upload", Package::TYPE_SCRIPT),
            array("w3af_find_captchas", Package::TYPE_SCRIPT),
            array("w3af_find_comments", Package::TYPE_SCRIPT),
            array("w3af_finger_bing", Package::TYPE_SCRIPT),
            array("w3af_finger_google", Package::TYPE_SCRIPT),
            array("w3af_finger_pks", Package::TYPE_SCRIPT),
            array("w3af_form_autocomplete", Package::TYPE_SCRIPT),
            array("w3af_get_mails", Package::TYPE_SCRIPT),
            array("w3af_ghdb", Package::TYPE_SCRIPT),
            array("w3af_halberd", Package::TYPE_SCRIPT),
            array("w3af_hash_find", Package::TYPE_SCRIPT),
            array("w3af_http_auth_detect", Package::TYPE_SCRIPT),
            array("w3af_http_in_body", Package::TYPE_SCRIPT),
            array("w3af_meta_tags", Package::TYPE_SCRIPT),
            array("w3af_objects", Package::TYPE_SCRIPT),
            array("w3af_path_disclosure", Package::TYPE_SCRIPT),
            array("w3af_phishtank", Package::TYPE_SCRIPT),
            array("w3af_private_ip", Package::TYPE_SCRIPT),
            array("w3af_ria_enumerator", Package::TYPE_SCRIPT),
            array("w3af_robots_reader", Package::TYPE_SCRIPT),
            array("w3af_sitemap_reader", Package::TYPE_SCRIPT),
            array("w3af_strange_http_code", Package::TYPE_SCRIPT),
            array("w3af_svn_users", Package::TYPE_SCRIPT),
            array("w3af_zone_h", Package::TYPE_SCRIPT),
            array("web_http_methods", Package::TYPE_SCRIPT),
            array("web_sql_xss", Package::TYPE_SCRIPT),
            array("websearch_client_domains", Package::TYPE_SCRIPT),
            array("webserver_error_msg", Package::TYPE_SCRIPT),
            array("webserver_files", Package::TYPE_SCRIPT),
            array("webserver_ssl", Package::TYPE_SCRIPT),
            array("www_auth_scanner", Package::TYPE_SCRIPT),
            array("www_dir_scanner", Package::TYPE_SCRIPT),
            array("www_file_scanner", Package::TYPE_SCRIPT),
        );

        foreach ($packageData as $package) {
            $this->insert("packages", array(
                "name" => $package[0],
                "type" => $package[1],
                "version" => "1.7",
                "system" => true,
                "status" => Package::STATUS_INSTALLED
            ));
        }

        return true;
	}

    /**
     * Down migration
     * @return bool
     */
    public function safeDown() {
        $this->truncateTable("packages");
		return true;
	}
}