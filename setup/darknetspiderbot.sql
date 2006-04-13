-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Jeudi 13 Avril 2006 à 22:15
-- Version du serveur: 4.0.20
-- Version de PHP: 4.3.4
-- 
-- Base de données: `darknetspiderbot`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `freesites_informations`
-- 

CREATE TABLE `freesites_informations` (
  `id_freesites` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` text NOT NULL,
  UNIQUE KEY `id_freesites` (`id_freesites`)
) TYPE=MyISAM;

-- 
-- Contenu de la table `freesites_informations`
-- 


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
  `created` timestamp(14) NOT NULL,
  `last_update` timestamp(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key_value` (`key_value`),
  KEY `last_update` (`last_update`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `freesites_keys`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `freesites_urls`
-- 

CREATE TABLE `freesites_urls` (
  `id_freesites` smallint(6) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `status` enum('standby','retrieving','retrieved','error') NOT NULL default 'standby',
  KEY `id_freesites` (`id_freesites`)
) TYPE=MyISAM;

-- 
-- Contenu de la table `freesites_urls`
-- 

