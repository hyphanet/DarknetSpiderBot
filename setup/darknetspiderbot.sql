-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Samedi 03 Juin 2006 à 18:45
-- Version du serveur: 4.1.9
-- Version de PHP: 4.3.10
-- 
-- Base de données: `darknetspiderbot`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `freesites_informations`
-- 

CREATE TABLE `freesites_informations` (
  `id_freesite` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` text NOT NULL,
  UNIQUE KEY `id_freesite` (`id_freesite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `freesites_keys`
-- 

CREATE TABLE `freesites_keys` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `key_type` enum('CHK','SSK') NOT NULL default 'CHK',
  `key_value` varchar(255) NOT NULL default '',
  `site_name` varchar(255) NOT NULL default '',
  `edition` smallint(5) unsigned NOT NULL default '0',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key_value` (`key_value`),
  KEY `last_update` (`last_update`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `freesites_urls`
-- 

CREATE TABLE `freesites_urls` (
  `id_freesite` smallint(5) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `status` enum('standby','retrieving','retrieved','error') NOT NULL default 'standby',
  KEY `id_freesite` (`id_freesite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        