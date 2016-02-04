/*
 Navicat MySQL Data Transfer

 Source Server         : iamdash.uk.easy-server.com
 Source Server Version : 50095
 Source Host           : 87.239.18.178
 Source Database       : iamdash_main

 Target Server Version : 50095
 File Encoding         : utf-8

 Date: 02/15/2013 09:47:37 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `flickr_cache`
-- ----------------------------
DROP TABLE IF EXISTS `flickr_cache`;
CREATE TABLE `flickr_cache` (
  `request` char(35) NOT NULL,
  `response` mediumtext NOT NULL,
  `expiration` datetime NOT NULL,
  KEY `request` (`request`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `flickr_cache`
-- ----------------------------
BEGIN;
INSERT INTO `flickr_cache` VALUES ('abfd01ee8605439134724576921da3ab', 'a:2:{s:6:\"photos\";a:5:{s:4:\"page\";i:1;s:5:\"pages\";d:747;s:7:\"perpage\";i:1;s:5:\"total\";s:3:\"747\";s:5:\"photo\";a:1:{i:0;a:12:{s:2:\"id\";s:10:\"8235291083\";s:5:\"owner\";s:12:\"53572347@N02\";s:6:\"secret\";s:10:\"7b4024bc9d\";s:6:\"server\";s:4:\"8338\";s:4:\"farm\";d:9;s:5:\"title\";s:9:\"photo.JPG\";s:8:\"ispublic\";i:1;s:8:\"isfriend\";i:0;s:8:\"isfamily\";i:0;s:5:\"url_o\";s:62:\"http://farm9.staticflickr.com/8338/8235291083_6bcd3d9058_o.jpg\";s:8:\"height_o\";s:4:\"2048\";s:7:\"width_o\";s:4:\"2048\";}}}s:4:\"stat\";s:2:\"ok\";}', '2013-02-15 03:39:30');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
