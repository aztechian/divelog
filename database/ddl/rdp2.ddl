--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 38620)
-- Name: rdp2; Type: TABLE; Schema: public; Owner: ian
--

CREATE TABLE rdp2 (
    init_press_group character(1),
    final_press_group character(1),
    min_time interval hour to minute,
    max_time interval hour to minute
) WITHOUT OIDS;


