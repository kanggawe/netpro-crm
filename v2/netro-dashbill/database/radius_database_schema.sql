-- ==========================================================
-- NETPRO CRM / FreeRADIUS 3.0 — Dedicated PostgreSQL Schema
-- Official PostgreSQL Schema for FreeRADIUS Engine
-- ==========================================================

/*
 * Table structure for table 'radacct'
 */
CREATE TABLE IF NOT EXISTS radacct (
	RadAcctId		bigserial PRIMARY KEY,
	AcctSessionId		text NOT NULL,
	AcctUniqueId		text NOT NULL UNIQUE,
	UserName		text,
	GroupName		text,
	Realm			text,
	NASIPAddress		inet NOT NULL,
	NASPortId		text,
	NASPortType		text,
	AcctStartTime		timestamp with time zone,
	AcctUpdateTime		timestamp with time zone,
	AcctStopTime		timestamp with time zone,
	AcctInterval		bigint,
	AcctSessionTime		bigint,
	AcctAuthentic		text,
	ConnectInfo_start	text,
	ConnectInfo_stop	text,
	AcctInputOctets		bigint,
	AcctOutputOctets	bigint,
	CalledStationId		text,
	CallingStationId	text,
	AcctTerminateCause	text,
	ServiceType		text,
	FramedProtocol		text,
	FramedIPAddress		inet,
	FramedIPv6Address	inet,
	FramedIPv6Prefix	inet,
	FramedInterfaceId	text,
	DelegatedIPv6Prefix	inet
);

CREATE INDEX IF NOT EXISTS radacct_active_session_idx ON radacct (AcctUniqueId) WHERE AcctStopTime IS NULL;
CREATE INDEX IF NOT EXISTS radacct_bulk_close ON radacct (NASIPAddress, AcctStartTime) WHERE AcctStopTime IS NULL;
CREATE INDEX IF NOT EXISTS radacct_bulk_timeout ON radacct (AcctStopTime NULLS FIRST, AcctUpdateTime);
CREATE INDEX IF NOT EXISTS radacct_start_user_idx ON radacct (AcctStartTime, UserName);

/*
 * Table structure for table 'radcheck'
 */
CREATE TABLE IF NOT EXISTS radcheck (
	id			serial PRIMARY KEY,
	UserName		text NOT NULL DEFAULT '',
	Attribute		text NOT NULL DEFAULT '',
	op			VARCHAR(2) NOT NULL DEFAULT '==',
	Value			text NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS radcheck_UserName ON radcheck (UserName, Attribute);

/*
 * Table structure for table 'radgroupcheck'
 */
CREATE TABLE IF NOT EXISTS radgroupcheck (
	id			serial PRIMARY KEY,
	GroupName		text NOT NULL DEFAULT '',
	Attribute		text NOT NULL DEFAULT '',
	op			VARCHAR(2) NOT NULL DEFAULT '==',
	Value			text NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS radgroupcheck_GroupName ON radgroupcheck (GroupName, Attribute);

/*
 * Table structure for table 'radgroupreply'
 */
CREATE TABLE IF NOT EXISTS radgroupreply (
	id			serial PRIMARY KEY,
	GroupName		text NOT NULL DEFAULT '',
	Attribute		text NOT NULL DEFAULT '',
	op			VARCHAR(2) NOT NULL DEFAULT '=',
	Value			text NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS radgroupreply_GroupName ON radgroupreply (GroupName, Attribute);

/*
 * Table structure for table 'radreply'
 */
CREATE TABLE IF NOT EXISTS radreply (
	id			serial PRIMARY KEY,
	UserName		text NOT NULL DEFAULT '',
	Attribute		text NOT NULL DEFAULT '',
	op			VARCHAR(2) NOT NULL DEFAULT '=',
	Value			text NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS radreply_UserName ON radreply (UserName, Attribute);

/*
 * Table structure for table 'radusergroup'
 */
CREATE TABLE IF NOT EXISTS radusergroup (
	id			serial PRIMARY KEY,
	UserName		text NOT NULL DEFAULT '',
	GroupName		text NOT NULL DEFAULT '',
	priority		integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS radusergroup_UserName ON radusergroup (UserName);

/*
 * Table structure for table 'radpostauth'
 */
CREATE TABLE IF NOT EXISTS radpostauth (
	id			bigserial PRIMARY KEY,
	username		text NOT NULL,
	pass			text,
	reply			text,
	CalledStationId		text,
	CallingStationId	text,
	authdate		timestamp with time zone NOT NULL DEFAULT now()
);

/*
 * Table structure for table 'nas' (MikroTik / Cisco / Huawei Router NAS)
 */
CREATE TABLE IF NOT EXISTS nas (
	id			serial PRIMARY KEY,
	nasname			text NOT NULL,
	shortname		text NOT NULL,
	type			text NOT NULL DEFAULT 'other',
	ports			integer,
	secret			text NOT NULL,
	server			text,
	community		text,
	description		text
);
CREATE INDEX IF NOT EXISTS nas_nasname ON nas (nasname);

-- ==========================================================
-- NETPRO CRM Management Bridge Tables (UI Metadata)
-- ==========================================================

CREATE TABLE IF NOT EXISTS radius_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    customer_name VARCHAR(150),
    profile_name VARCHAR(100),
    ip_address VARCHAR(50),
    nas_name VARCHAR(100),
    status VARCHAR(20) DEFAULT 'ACTIVE',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS radius_profiles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    rate_limit VARCHAR(50) NOT NULL,
    burst_limit VARCHAR(50),
    pool_name VARCHAR(50),
    user_count INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS radius_vouchers (
    id SERIAL PRIMARY KEY,
    batch_code VARCHAR(50) UNIQUE NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    duration VARCHAR(50),
    qty INT DEFAULT 100,
    price NUMERIC(15,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'GENERATED',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Initial NAS & Profiles Seeds
INSERT INTO nas (nasname, shortname, type, secret, description) VALUES
('192.168.88.1', 'MikroTik-CCR2004-Core', 'mikrotik', 'netpro_radius_secret_2026', 'Main BNG / PPPoE Gateway Router'),
('10.10.10.1', 'MikroTik-RB4011-Hotspot', 'mikrotik', 'netpro_radius_secret_2026', 'Hotspot Gateway Public Area')
ON CONFLICT (id) DO NOTHING;

INSERT INTO radius_profiles (name, rate_limit, pool_name) VALUES
('Home-Lite-10M', '10M/10M', 'pool-pppoe-home'),
('Home-Basic-20M', '20M/20M', 'pool-pppoe-home'),
('Home-Premium-50M', '50M/50M', 'pool-pppoe-home'),
('SOHO-Pro-100M', '100M/100M', 'pool-pppoe-soho')
ON CONFLICT (name) DO NOTHING;
