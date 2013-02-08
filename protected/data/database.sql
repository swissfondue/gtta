--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.13
-- Dumped by pg_dump version 9.1.3
-- Started on 2013-02-08 15:50:46 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 498 (class 1247 OID 21583)
-- Dependencies: 6
-- Name: check_rating; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_rating AS ENUM (
    'hidden',
    'info',
    'low_risk',
    'med_risk',
    'high_risk'
);


ALTER TYPE public.check_rating OWNER TO postgres;

--
-- TOC entry 501 (class 1247 OID 21590)
-- Dependencies: 6
-- Name: check_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_status AS ENUM (
    'open',
    'in_progress',
    'stop',
    'finished'
);


ALTER TYPE public.check_status OWNER TO postgres;

--
-- TOC entry 694 (class 1247 OID 29313)
-- Dependencies: 6
-- Name: input_type; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE input_type AS ENUM (
    'text',
    'textarea',
    'checkbox',
    'radio'
);


ALTER TYPE public.input_type OWNER TO postgres;

--
-- TOC entry 504 (class 1247 OID 21596)
-- Dependencies: 6
-- Name: project_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE project_status AS ENUM (
    'open',
    'in_progress',
    'finished'
);


ALTER TYPE public.project_status OWNER TO gtta;

--
-- TOC entry 507 (class 1247 OID 21601)
-- Dependencies: 6
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE user_role AS ENUM (
    'admin',
    'user',
    'client'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- TOC entry 656 (class 1247 OID 26942)
-- Dependencies: 6
-- Name: vuln_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE vuln_status AS ENUM (
    'open',
    'resolved'
);


ALTER TYPE public.vuln_status OWNER TO gtta;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 140 (class 1259 OID 21605)
-- Dependencies: 6
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- TOC entry 141 (class 1259 OID 21611)
-- Dependencies: 6 140
-- Name: check_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_categories_id_seq OWNER TO gtta;

--
-- TOC entry 2239 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 142 (class 1259 OID 21613)
-- Dependencies: 6
-- Name: check_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories_l10n (
    check_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_categories_l10n OWNER TO gtta;

--
-- TOC entry 143 (class 1259 OID 21619)
-- Dependencies: 2030 6
-- Name: check_controls; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls (
    id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_controls OWNER TO gtta;

--
-- TOC entry 144 (class 1259 OID 21625)
-- Dependencies: 143 6
-- Name: check_controls_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_controls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_controls_id_seq OWNER TO gtta;

--
-- TOC entry 2240 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- TOC entry 145 (class 1259 OID 21627)
-- Dependencies: 6
-- Name: check_controls_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls_l10n (
    check_control_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_controls_l10n OWNER TO gtta;

--
-- TOC entry 146 (class 1259 OID 21633)
-- Dependencies: 2031 2033 6 694
-- Name: check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    description character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    value character varying,
    type input_type DEFAULT 'text'::input_type NOT NULL
);


ALTER TABLE public.check_inputs OWNER TO gtta;

--
-- TOC entry 147 (class 1259 OID 21640)
-- Dependencies: 146 6
-- Name: check_inputs_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_inputs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_inputs_id_seq OWNER TO gtta;

--
-- TOC entry 2241 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 148 (class 1259 OID 21642)
-- Dependencies: 6
-- Name: check_inputs_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs_l10n (
    check_input_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    description character varying,
    value character varying
);


ALTER TABLE public.check_inputs_l10n OWNER TO gtta;

--
-- TOC entry 149 (class 1259 OID 21648)
-- Dependencies: 2034 6
-- Name: check_results; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_results OWNER TO gtta;

--
-- TOC entry 150 (class 1259 OID 21655)
-- Dependencies: 6 149
-- Name: check_results_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_results_id_seq OWNER TO gtta;

--
-- TOC entry 2242 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 151 (class 1259 OID 21657)
-- Dependencies: 6
-- Name: check_results_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results_l10n (
    check_result_id bigint NOT NULL,
    language_id bigint NOT NULL,
    result character varying,
    title character varying(1000)
);


ALTER TABLE public.check_results_l10n OWNER TO gtta;

--
-- TOC entry 152 (class 1259 OID 21663)
-- Dependencies: 2036 6
-- Name: check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    solution character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_solutions OWNER TO gtta;

--
-- TOC entry 153 (class 1259 OID 21670)
-- Dependencies: 152 6
-- Name: check_solutions_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_solutions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_solutions_id_seq OWNER TO gtta;

--
-- TOC entry 2243 (class 0 OID 0)
-- Dependencies: 153
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 154 (class 1259 OID 21672)
-- Dependencies: 6
-- Name: check_solutions_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions_l10n (
    check_solution_id bigint NOT NULL,
    language_id bigint NOT NULL,
    solution character varying,
    title character varying(1000)
);


ALTER TABLE public.check_solutions_l10n OWNER TO gtta;

--
-- TOC entry 155 (class 1259 OID 21678)
-- Dependencies: 2038 2040 6
-- Name: checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks (
    id bigint NOT NULL,
    check_control_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    background_info character varying,
    hints character varying,
    advanced boolean NOT NULL,
    automated boolean NOT NULL,
    script character varying(1000),
    multiple_solutions boolean NOT NULL,
    protocol character varying(1000),
    port integer,
    question character varying,
    reference_id bigint NOT NULL,
    reference_code character varying(1000),
    reference_url character varying(1000),
    effort integer DEFAULT 0 NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- TOC entry 156 (class 1259 OID 21685)
-- Dependencies: 6 155
-- Name: checks_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.checks_id_seq OWNER TO gtta;

--
-- TOC entry 2244 (class 0 OID 0)
-- Dependencies: 156
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 157 (class 1259 OID 21687)
-- Dependencies: 6
-- Name: checks_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks_l10n (
    check_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    background_info character varying,
    hints character varying,
    reference character varying,
    question character varying
);


ALTER TABLE public.checks_l10n OWNER TO gtta;

--
-- TOC entry 158 (class 1259 OID 21693)
-- Dependencies: 6
-- Name: clients; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE clients (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    country character varying(1000),
    state character varying(1000),
    city character varying(1000),
    address character varying(1000),
    postcode character varying(1000),
    website character varying(1000),
    contact_name character varying(1000),
    contact_phone character varying(1000),
    contact_email character varying(1000),
    contact_fax character varying(1000),
    logo_path character varying(1000),
    logo_type character varying(1000)
);


ALTER TABLE public.clients OWNER TO gtta;

--
-- TOC entry 159 (class 1259 OID 21699)
-- Dependencies: 158 6
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO gtta;

--
-- TOC entry 2245 (class 0 OID 0)
-- Dependencies: 159
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 160 (class 1259 OID 21701)
-- Dependencies: 2042 2043 6
-- Name: emails; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE emails (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying NOT NULL,
    attempts integer DEFAULT 0 NOT NULL,
    sent boolean DEFAULT false NOT NULL
);


ALTER TABLE public.emails OWNER TO gtta;

--
-- TOC entry 161 (class 1259 OID 21709)
-- Dependencies: 160 6
-- Name: emails_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.emails_id_seq OWNER TO gtta;

--
-- TOC entry 2246 (class 0 OID 0)
-- Dependencies: 161
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- TOC entry 162 (class 1259 OID 21711)
-- Dependencies: 6
-- Name: languages; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE languages (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    code character(2) NOT NULL,
    "default" boolean NOT NULL
);


ALTER TABLE public.languages OWNER TO gtta;

--
-- TOC entry 163 (class 1259 OID 21717)
-- Dependencies: 6 162
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.languages_id_seq OWNER TO gtta;

--
-- TOC entry 2247 (class 0 OID 0)
-- Dependencies: 163
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 202 (class 1259 OID 28213)
-- Dependencies: 2076 6
-- Name: login_history; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE login_history (
    id bigint NOT NULL,
    user_id bigint,
    user_name character varying(1000) NOT NULL,
    create_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.login_history OWNER TO gtta;

--
-- TOC entry 201 (class 1259 OID 28211)
-- Dependencies: 202 6
-- Name: login_history_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE login_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.login_history_id_seq OWNER TO gtta;

--
-- TOC entry 2248 (class 0 OID 0)
-- Dependencies: 201
-- Name: login_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE login_history_id_seq OWNED BY login_history.id;


--
-- TOC entry 164 (class 1259 OID 21719)
-- Dependencies: 6
-- Name: project_details; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_details (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying
);


ALTER TABLE public.project_details OWNER TO gtta;

--
-- TOC entry 165 (class 1259 OID 21725)
-- Dependencies: 6 164
-- Name: project_details_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE project_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_details_id_seq OWNER TO gtta;

--
-- TOC entry 2249 (class 0 OID 0)
-- Dependencies: 165
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 187 (class 1259 OID 26344)
-- Dependencies: 2066 6
-- Name: project_users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_users (
    project_id bigint NOT NULL,
    user_id bigint NOT NULL,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_users OWNER TO gtta;

--
-- TOC entry 166 (class 1259 OID 21727)
-- Dependencies: 2047 6 504
-- Name: projects; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    year character(4) NOT NULL,
    deadline date NOT NULL,
    name character varying(1000) NOT NULL,
    status project_status DEFAULT 'open'::project_status NOT NULL,
    vuln_overdue date
);


ALTER TABLE public.projects OWNER TO gtta;

--
-- TOC entry 167 (class 1259 OID 21734)
-- Dependencies: 6 166
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO gtta;

--
-- TOC entry 2250 (class 0 OID 0)
-- Dependencies: 167
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 168 (class 1259 OID 21736)
-- Dependencies: 6
-- Name: references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE "references" (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    url character varying(1000)
);


ALTER TABLE public."references" OWNER TO gtta;

--
-- TOC entry 169 (class 1259 OID 21742)
-- Dependencies: 6 168
-- Name: references_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.references_id_seq OWNER TO gtta;

--
-- TOC entry 2251 (class 0 OID 0)
-- Dependencies: 169
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- TOC entry 199 (class 1259 OID 28055)
-- Dependencies: 2074 6
-- Name: report_template_sections; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections (
    id bigint NOT NULL,
    report_template_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    intro character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections OWNER TO gtta;

--
-- TOC entry 198 (class 1259 OID 28053)
-- Dependencies: 6 199
-- Name: report_template_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_template_sections_id_seq OWNER TO gtta;

--
-- TOC entry 2252 (class 0 OID 0)
-- Dependencies: 198
-- Name: report_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_sections_id_seq OWNED BY report_template_sections.id;


--
-- TOC entry 200 (class 1259 OID 28075)
-- Dependencies: 6
-- Name: report_template_sections_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections_l10n (
    report_template_section_id bigint NOT NULL,
    language_id bigint NOT NULL,
    intro character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections_l10n OWNER TO gtta;

--
-- TOC entry 196 (class 1259 OID 27336)
-- Dependencies: 2071 2072 6
-- Name: report_template_summary; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary (
    id bigint NOT NULL,
    summary character varying,
    rating_from numeric(3,2) DEFAULT 0.00 NOT NULL,
    rating_to numeric(3,2) DEFAULT 0.00 NOT NULL,
    report_template_id bigint NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary OWNER TO gtta;

--
-- TOC entry 195 (class 1259 OID 27334)
-- Dependencies: 196 6
-- Name: report_template_summary_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_summary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_template_summary_id_seq OWNER TO gtta;

--
-- TOC entry 2253 (class 0 OID 0)
-- Dependencies: 195
-- Name: report_template_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_summary_id_seq OWNED BY report_template_summary.id;


--
-- TOC entry 197 (class 1259 OID 27347)
-- Dependencies: 6
-- Name: report_template_summary_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary_l10n (
    report_template_summary_id bigint NOT NULL,
    language_id bigint NOT NULL,
    summary character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary_l10n OWNER TO gtta;

--
-- TOC entry 193 (class 1259 OID 27306)
-- Dependencies: 6
-- Name: report_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates (
    id bigint NOT NULL,
    name character varying(1000),
    header_image_path character varying(1000),
    header_image_type character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying
);


ALTER TABLE public.report_templates OWNER TO gtta;

--
-- TOC entry 192 (class 1259 OID 27304)
-- Dependencies: 193 6
-- Name: report_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_templates_id_seq OWNER TO gtta;

--
-- TOC entry 2254 (class 0 OID 0)
-- Dependencies: 192
-- Name: report_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_templates_id_seq OWNED BY report_templates.id;


--
-- TOC entry 194 (class 1259 OID 27315)
-- Dependencies: 6
-- Name: report_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates_l10n (
    report_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying
);


ALTER TABLE public.report_templates_l10n OWNER TO gtta;

--
-- TOC entry 183 (class 1259 OID 22073)
-- Dependencies: 6
-- Name: risk_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories (
    id bigint NOT NULL,
    name character varying(1000),
    risk_template_id bigint NOT NULL
);


ALTER TABLE public.risk_categories OWNER TO gtta;

--
-- TOC entry 182 (class 1259 OID 22071)
-- Dependencies: 6 183
-- Name: risk_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.risk_categories_id_seq OWNER TO gtta;

--
-- TOC entry 2255 (class 0 OID 0)
-- Dependencies: 182
-- Name: risk_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_categories_id_seq OWNED BY risk_categories.id;


--
-- TOC entry 184 (class 1259 OID 22082)
-- Dependencies: 6
-- Name: risk_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories_l10n (
    risk_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_categories_l10n OWNER TO gtta;

--
-- TOC entry 185 (class 1259 OID 24899)
-- Dependencies: 2064 2065 6
-- Name: risk_category_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_category_checks (
    risk_category_id bigint NOT NULL,
    check_id bigint NOT NULL,
    damage integer DEFAULT 0 NOT NULL,
    likelihood integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.risk_category_checks OWNER TO gtta;

--
-- TOC entry 189 (class 1259 OID 26361)
-- Dependencies: 6
-- Name: risk_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates (
    id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates OWNER TO gtta;

--
-- TOC entry 188 (class 1259 OID 26359)
-- Dependencies: 6 189
-- Name: risk_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.risk_templates_id_seq OWNER TO gtta;

--
-- TOC entry 2256 (class 0 OID 0)
-- Dependencies: 188
-- Name: risk_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_templates_id_seq OWNED BY risk_templates.id;


--
-- TOC entry 190 (class 1259 OID 26370)
-- Dependencies: 6
-- Name: risk_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates_l10n (
    risk_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates_l10n OWNER TO gtta;

--
-- TOC entry 186 (class 1259 OID 26336)
-- Dependencies: 6
-- Name: sessions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE sessions (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.sessions OWNER TO gtta;

--
-- TOC entry 170 (class 1259 OID 21744)
-- Dependencies: 6
-- Name: system; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE system (
    id bigint NOT NULL,
    backup timestamp without time zone
);


ALTER TABLE public.system OWNER TO gtta;

--
-- TOC entry 171 (class 1259 OID 21747)
-- Dependencies: 6 170
-- Name: system_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE system_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.system_id_seq OWNER TO gtta;

--
-- TOC entry 2257 (class 0 OID 0)
-- Dependencies: 171
-- Name: system_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE system_id_seq OWNED BY system.id;


--
-- TOC entry 172 (class 1259 OID 21749)
-- Dependencies: 2051 6
-- Name: target_check_attachments; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_attachments (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    type character varying(1000) NOT NULL,
    path character varying(1000) NOT NULL,
    size bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_attachments OWNER TO gtta;

--
-- TOC entry 173 (class 1259 OID 21756)
-- Dependencies: 2052 2053 2054 2055 2056 2057 6
-- Name: target_check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_categories (
    target_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    advanced boolean NOT NULL,
    check_count bigint DEFAULT 0 NOT NULL,
    finished_count bigint DEFAULT 0 NOT NULL,
    low_risk_count bigint DEFAULT 0 NOT NULL,
    med_risk_count bigint DEFAULT 0 NOT NULL,
    high_risk_count bigint DEFAULT 0 NOT NULL,
    info_count bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_categories OWNER TO gtta;

--
-- TOC entry 174 (class 1259 OID 21764)
-- Dependencies: 6
-- Name: target_check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_inputs (
    target_id bigint NOT NULL,
    check_input_id bigint NOT NULL,
    value character varying,
    file character varying(1000),
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_inputs OWNER TO gtta;

--
-- TOC entry 175 (class 1259 OID 21770)
-- Dependencies: 6
-- Name: target_check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_solutions (
    target_id bigint NOT NULL,
    check_solution_id bigint NOT NULL,
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_solutions OWNER TO gtta;

--
-- TOC entry 191 (class 1259 OID 26906)
-- Dependencies: 2068 6 656
-- Name: target_check_vulns; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_vulns (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    user_id bigint,
    deadline date,
    status vuln_status DEFAULT 'open'::vuln_status NOT NULL
);


ALTER TABLE public.target_check_vulns OWNER TO gtta;

--
-- TOC entry 176 (class 1259 OID 21773)
-- Dependencies: 2058 501 6 498
-- Name: target_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_checks (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying,
    target_file character varying(1000),
    rating check_rating,
    started timestamp without time zone,
    pid bigint,
    status check_status DEFAULT 'open'::check_status NOT NULL,
    result_file character varying(1000),
    language_id bigint NOT NULL,
    protocol character varying(1000),
    port integer,
    override_target character varying(1000),
    user_id bigint NOT NULL,
    table_result character varying
);


ALTER TABLE public.target_checks OWNER TO gtta;

--
-- TOC entry 177 (class 1259 OID 21780)
-- Dependencies: 6
-- Name: target_references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_references (
    target_id bigint NOT NULL,
    reference_id bigint NOT NULL
);


ALTER TABLE public.target_references OWNER TO gtta;

--
-- TOC entry 178 (class 1259 OID 21783)
-- Dependencies: 6
-- Name: targets; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE targets (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    host character varying(1000) NOT NULL,
    description character varying(1000)
);


ALTER TABLE public.targets OWNER TO gtta;

--
-- TOC entry 179 (class 1259 OID 21789)
-- Dependencies: 6 178
-- Name: targets_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.targets_id_seq OWNER TO gtta;

--
-- TOC entry 2258 (class 0 OID 0)
-- Dependencies: 179
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 180 (class 1259 OID 21791)
-- Dependencies: 2060 2062 6 507
-- Name: users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    email character varying(1000) NOT NULL,
    password character varying(1000) NOT NULL,
    name character varying(1000),
    client_id bigint,
    role user_role DEFAULT 'admin'::user_role NOT NULL,
    last_action_time timestamp without time zone,
    send_notifications boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO gtta;

--
-- TOC entry 181 (class 1259 OID 21798)
-- Dependencies: 6 180
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO gtta;

--
-- TOC entry 2259 (class 0 OID 0)
-- Dependencies: 181
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2028 (class 2604 OID 21800)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 2029 (class 2604 OID 21801)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- TOC entry 2032 (class 2604 OID 21802)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 2035 (class 2604 OID 21803)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 2037 (class 2604 OID 21804)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 2039 (class 2604 OID 21805)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 2041 (class 2604 OID 21806)
-- Dependencies: 159 158
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 2044 (class 2604 OID 21807)
-- Dependencies: 161 160
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- TOC entry 2045 (class 2604 OID 21808)
-- Dependencies: 163 162
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 2075 (class 2604 OID 28216)
-- Dependencies: 201 202 202
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history ALTER COLUMN id SET DEFAULT nextval('login_history_id_seq'::regclass);


--
-- TOC entry 2046 (class 2604 OID 21809)
-- Dependencies: 165 164
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 2048 (class 2604 OID 21810)
-- Dependencies: 167 166
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 2049 (class 2604 OID 21811)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- TOC entry 2073 (class 2604 OID 28058)
-- Dependencies: 199 198 199
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections ALTER COLUMN id SET DEFAULT nextval('report_template_sections_id_seq'::regclass);


--
-- TOC entry 2070 (class 2604 OID 27339)
-- Dependencies: 195 196 196
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary ALTER COLUMN id SET DEFAULT nextval('report_template_summary_id_seq'::regclass);


--
-- TOC entry 2069 (class 2604 OID 27309)
-- Dependencies: 193 192 193
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates ALTER COLUMN id SET DEFAULT nextval('report_templates_id_seq'::regclass);


--
-- TOC entry 2063 (class 2604 OID 22076)
-- Dependencies: 182 183 183
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories ALTER COLUMN id SET DEFAULT nextval('risk_categories_id_seq'::regclass);


--
-- TOC entry 2067 (class 2604 OID 26364)
-- Dependencies: 188 189 189
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates ALTER COLUMN id SET DEFAULT nextval('risk_templates_id_seq'::regclass);


--
-- TOC entry 2050 (class 2604 OID 21812)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY system ALTER COLUMN id SET DEFAULT nextval('system_id_seq'::regclass);


--
-- TOC entry 2059 (class 2604 OID 21813)
-- Dependencies: 179 178
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 2061 (class 2604 OID 21814)
-- Dependencies: 181 180
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2080 (class 2606 OID 21816)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 2078 (class 2606 OID 21818)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2084 (class 2606 OID 21820)
-- Dependencies: 145 145 145
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- TOC entry 2082 (class 2606 OID 21822)
-- Dependencies: 143 143
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- TOC entry 2088 (class 2606 OID 21824)
-- Dependencies: 148 148 148
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 2086 (class 2606 OID 21826)
-- Dependencies: 146 146
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 2092 (class 2606 OID 21828)
-- Dependencies: 151 151 151
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 2090 (class 2606 OID 21830)
-- Dependencies: 149 149
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 2096 (class 2606 OID 21832)
-- Dependencies: 154 154 154
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 2094 (class 2606 OID 21834)
-- Dependencies: 152 152
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 2100 (class 2606 OID 21836)
-- Dependencies: 157 157 157
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 2098 (class 2606 OID 21838)
-- Dependencies: 155 155
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 2102 (class 2606 OID 21840)
-- Dependencies: 158 158
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 2104 (class 2606 OID 21842)
-- Dependencies: 160 160
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- TOC entry 2106 (class 2606 OID 21844)
-- Dependencies: 162 162
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 2108 (class 2606 OID 21846)
-- Dependencies: 162 162
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 2110 (class 2606 OID 21848)
-- Dependencies: 162 162
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 2168 (class 2606 OID 28222)
-- Dependencies: 202 202
-- Name: login_history_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2112 (class 2606 OID 21850)
-- Dependencies: 164 164
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 2146 (class 2606 OID 26348)
-- Dependencies: 187 187 187
-- Name: project_users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_pkey PRIMARY KEY (project_id, user_id);


--
-- TOC entry 2114 (class 2606 OID 21852)
-- Dependencies: 166 166
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 2116 (class 2606 OID 21854)
-- Dependencies: 168 168
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- TOC entry 2166 (class 2606 OID 28082)
-- Dependencies: 200 200 200
-- Name: report_template_sections_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_pkey PRIMARY KEY (report_template_section_id, language_id);


--
-- TOC entry 2162 (class 2606 OID 28064)
-- Dependencies: 199 199
-- Name: report_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_pkey PRIMARY KEY (id);


--
-- TOC entry 2164 (class 2606 OID 28164)
-- Dependencies: 199 199 199
-- Name: report_template_sections_report_template_id_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_key UNIQUE (report_template_id, check_category_id);


--
-- TOC entry 2160 (class 2606 OID 27354)
-- Dependencies: 197 197 197
-- Name: report_template_summary_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_pkey PRIMARY KEY (report_template_summary_id, language_id);


--
-- TOC entry 2158 (class 2606 OID 27346)
-- Dependencies: 196 196
-- Name: report_template_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_pkey PRIMARY KEY (id);


--
-- TOC entry 2156 (class 2606 OID 27322)
-- Dependencies: 194 194 194
-- Name: report_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_pkey PRIMARY KEY (report_template_id, language_id);


--
-- TOC entry 2154 (class 2606 OID 27314)
-- Dependencies: 193 193
-- Name: report_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates
    ADD CONSTRAINT report_templates_pkey PRIMARY KEY (id);


--
-- TOC entry 2140 (class 2606 OID 22089)
-- Dependencies: 184 184 184
-- Name: risk_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_pkey PRIMARY KEY (risk_category_id, language_id);


--
-- TOC entry 2138 (class 2606 OID 22081)
-- Dependencies: 183 183
-- Name: risk_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2142 (class 2606 OID 24903)
-- Dependencies: 185 185 185
-- Name: risk_category_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_pkey PRIMARY KEY (risk_category_id, check_id);


--
-- TOC entry 2150 (class 2606 OID 26377)
-- Dependencies: 190 190 190
-- Name: risk_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_pkey PRIMARY KEY (risk_template_id, language_id);


--
-- TOC entry 2148 (class 2606 OID 26369)
-- Dependencies: 189 189
-- Name: risk_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates
    ADD CONSTRAINT risk_templates_pkey PRIMARY KEY (id);


--
-- TOC entry 2144 (class 2606 OID 26343)
-- Dependencies: 186 186
-- Name: sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 2118 (class 2606 OID 21856)
-- Dependencies: 170 170
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY system
    ADD CONSTRAINT system_pkey PRIMARY KEY (id);


--
-- TOC entry 2120 (class 2606 OID 21858)
-- Dependencies: 172 172
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 2122 (class 2606 OID 21860)
-- Dependencies: 173 173 173
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 2124 (class 2606 OID 21862)
-- Dependencies: 174 174 174
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 2126 (class 2606 OID 21864)
-- Dependencies: 175 175 175
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 2152 (class 2606 OID 26910)
-- Dependencies: 191 191 191
-- Name: target_check_vulns_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2128 (class 2606 OID 21866)
-- Dependencies: 176 176 176
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2130 (class 2606 OID 21868)
-- Dependencies: 177 177 177
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- TOC entry 2132 (class 2606 OID 21870)
-- Dependencies: 178 178
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 2134 (class 2606 OID 21872)
-- Dependencies: 180 180
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 2136 (class 2606 OID 21874)
-- Dependencies: 180 180
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2169 (class 2606 OID 21875)
-- Dependencies: 2077 140 142
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2170 (class 2606 OID 21880)
-- Dependencies: 2109 142 162
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2171 (class 2606 OID 28304)
-- Dependencies: 143 2077 140
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2172 (class 2606 OID 21890)
-- Dependencies: 2081 145 143
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2173 (class 2606 OID 21895)
-- Dependencies: 2109 145 162
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2174 (class 2606 OID 29326)
-- Dependencies: 2097 155 146
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2175 (class 2606 OID 21905)
-- Dependencies: 2085 146 148
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2176 (class 2606 OID 21910)
-- Dependencies: 162 148 2109
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2177 (class 2606 OID 22135)
-- Dependencies: 2097 155 149
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2178 (class 2606 OID 22105)
-- Dependencies: 2089 151 149
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2179 (class 2606 OID 22110)
-- Dependencies: 2109 151 162
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2180 (class 2606 OID 22130)
-- Dependencies: 2097 155 152
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2181 (class 2606 OID 22120)
-- Dependencies: 152 2093 154
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2182 (class 2606 OID 22125)
-- Dependencies: 2109 154 162
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2183 (class 2606 OID 28318)
-- Dependencies: 2081 155 143
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2185 (class 2606 OID 22150)
-- Dependencies: 157 2097 155
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2186 (class 2606 OID 22155)
-- Dependencies: 157 162 2109
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2184 (class 2606 OID 28323)
-- Dependencies: 168 2115 155
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2187 (class 2606 OID 21965)
-- Dependencies: 160 180 2135
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2233 (class 2606 OID 28242)
-- Dependencies: 2135 202 180
-- Name: login_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2188 (class 2606 OID 21970)
-- Dependencies: 2113 166 164
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2216 (class 2606 OID 26896)
-- Dependencies: 166 187 2113
-- Name: project_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2217 (class 2606 OID 26901)
-- Dependencies: 180 2135 187
-- Name: project_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2189 (class 2606 OID 26965)
-- Dependencies: 2101 158 166
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2229 (class 2606 OID 28153)
-- Dependencies: 199 2077 140
-- Name: report_template_sections_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2231 (class 2606 OID 28143)
-- Dependencies: 2109 162 200
-- Name: report_template_sections_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2232 (class 2606 OID 28148)
-- Dependencies: 2161 199 200
-- Name: report_template_sections_l10n_report_template_section_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_report_template_section_id_fkey FOREIGN KEY (report_template_section_id) REFERENCES report_template_sections(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2230 (class 2606 OID 28158)
-- Dependencies: 199 2153 193
-- Name: report_template_sections_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2227 (class 2606 OID 27375)
-- Dependencies: 2109 162 197
-- Name: report_template_summary_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2228 (class 2606 OID 27380)
-- Dependencies: 197 2157 196
-- Name: report_template_summary_l10n_report_template_summary_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_report_template_summary_id_fkey FOREIGN KEY (report_template_summary_id) REFERENCES report_template_summary(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2226 (class 2606 OID 28118)
-- Dependencies: 193 2153 196
-- Name: report_template_summary_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2224 (class 2606 OID 28175)
-- Dependencies: 2109 162 194
-- Name: report_templates_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2225 (class 2606 OID 28180)
-- Dependencies: 2153 194 193
-- Name: report_templates_l10n_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2213 (class 2606 OID 22095)
-- Dependencies: 162 184 2109
-- Name: risk_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2212 (class 2606 OID 22090)
-- Dependencies: 183 184 2137
-- Name: risk_categories_l10n_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2211 (class 2606 OID 26388)
-- Dependencies: 189 183 2147
-- Name: risk_categories_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2214 (class 2606 OID 24924)
-- Dependencies: 185 2097 155
-- Name: risk_category_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2215 (class 2606 OID 24929)
-- Dependencies: 2137 185 183
-- Name: risk_category_checks_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2219 (class 2606 OID 26383)
-- Dependencies: 189 190 2147
-- Name: risk_templates_l10n_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2218 (class 2606 OID 26378)
-- Dependencies: 162 190 2109
-- Name: risk_templatess_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templatess_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2190 (class 2606 OID 27274)
-- Dependencies: 155 172 2097
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2191 (class 2606 OID 27279)
-- Dependencies: 172 2131 178
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2192 (class 2606 OID 27284)
-- Dependencies: 176 172 172 2127 176
-- Name: target_check_attachments_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2193 (class 2606 OID 27294)
-- Dependencies: 2077 140 173
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2194 (class 2606 OID 27299)
-- Dependencies: 2131 178 173
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2195 (class 2606 OID 27254)
-- Dependencies: 2097 174 155
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2196 (class 2606 OID 27259)
-- Dependencies: 174 146 2085
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2197 (class 2606 OID 27264)
-- Dependencies: 2131 178 174
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2198 (class 2606 OID 27269)
-- Dependencies: 176 2127 176 174 174
-- Name: target_check_inputs_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2199 (class 2606 OID 27214)
-- Dependencies: 155 175 2097
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2200 (class 2606 OID 27219)
-- Dependencies: 152 175 2093
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2201 (class 2606 OID 27224)
-- Dependencies: 2131 178 175
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2202 (class 2606 OID 27229)
-- Dependencies: 2127 176 176 175 175
-- Name: target_check_solutions_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2220 (class 2606 OID 27194)
-- Dependencies: 155 2097 191
-- Name: target_check_vulns_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2221 (class 2606 OID 27199)
-- Dependencies: 191 178 2131
-- Name: target_check_vulns_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2223 (class 2606 OID 27209)
-- Dependencies: 2127 191 176 176 191
-- Name: target_check_vulns_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2222 (class 2606 OID 27204)
-- Dependencies: 180 2135 191
-- Name: target_check_vulns_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2203 (class 2606 OID 28329)
-- Dependencies: 155 2097 176
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2204 (class 2606 OID 28334)
-- Dependencies: 2109 176 162
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2205 (class 2606 OID 28339)
-- Dependencies: 176 178 2131
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2206 (class 2606 OID 28344)
-- Dependencies: 2135 180 176
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2207 (class 2606 OID 22050)
-- Dependencies: 2115 177 168
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2208 (class 2606 OID 22055)
-- Dependencies: 2131 177 178
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2209 (class 2606 OID 27169)
-- Dependencies: 2113 178 166
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2210 (class 2606 OID 28237)
-- Dependencies: 158 180 2101
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2238 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2013-02-08 15:50:47 MSK

--
-- PostgreSQL database dump complete
--

