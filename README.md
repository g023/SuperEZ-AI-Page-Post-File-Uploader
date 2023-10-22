# SuperEZ AI - Page/Post File Uploader Plugin


## Screenshots

Here is a screenshot of the admin screen:

![Screenshot](https://github.com/g023/SuperEZ-AI-Page-Post-File-Uploader/raw/main/screenshots/admin_screen.png)



**Plugin Name:** SuperEZ AI - Page/Post File Uploader

**Description:** Allow users to upload files to pages or posts.

**Version:** 0.1a

**Author:** [github.com/g023](https://github.com/g023)

**License:** 3-clause BSD

## Introduction

The SuperEZ AI - Page/Post File Uploader is a WordPress plugin that enables users to upload files directly to pages or posts, enhancing your content management capabilities. This README.md file provides an advanced overview of the plugin, offering detailed insights into its functionality and usage.

## Table of Contents

1. [Overview](#overview)
2. [Compatibility](#compatibility)
3. [Features](#features)
4. [Usage](#usage)
5. [Installation](#installation)
6. [Customization](#customization)
7. [Future Developments](#future-developments)
8. [Notes](#notes)

## 1. Overview

The SuperEZ AI - Page/Post File Uploader is designed to simplify the process of uploading files to your WordPress pages or posts. Unlike some other plugins, it utilizes the native WordPress media uploader, which ensures compatibility with future WordPress versions.

### Key Features

- Upload files to pages or posts.
- Utilizes the WordPress media uploader.
- Use short tags to display files on your page or post.
- Files can be labeled for easy identification.
- Dynamic, no need for manual refreshing.
- Supports setting files as private, preventing them from appearing in the short tag output.

## 2. Compatibility

This plugin has been tested and verified with WordPress version 6.3.2.

## 3. Features

### 3.1 Short Tags

The primary method for displaying uploaded files on your page or post is through short tags. The main short tag for this plugin is `[superez-ai-file]`.

### 3.2 Saving Changes

It's essential to note that any changes made, such as adding or removing files, are not saved until the user saves or updates the page or post using the main save/update button.

### 3.3 File Management

The plugin does not handle file deletion from your server; this process is managed through the WordPress media manager. Removing a file from a page or post will not delete the file itself.

### 3.4 Labeling Files

You have the option to add labels to your uploaded files, although this is not mandatory. If you choose not to add a label, the short tag will use the shortname of the file (the name after the last slash in the URL) as the label.

### 3.5 Private Files

Setting a file as private will prevent it from being displayed in the short tag output. However, please note that the file remains publicly accessible directly via its URL.

## 4. Usage

1. Install and activate the plugin as described in the [Installation](#installation) section.
2. Use the `[superez-ai-file]` short tag in your posts or pages to display your uploaded files.
3. To add or remove files, edit your page or post and use the main save/update button to save changes.

## 5. Installation

To install the SuperEZ AI - Page/Post File Uploader Plugin manually.
1. create a folder called 'superez-ai-file' inside your 'wp-content/plugins/' directory
2. copy the plugin structure to the created directory.
   - eg: wp-content/plugins/superez-ai-file/superez-ai-file.php
         wp-content/plugins/superez-ai-file/superez-ai-file.php
         wp-content/plugins/superez-ai-file/_inc/template.main.php

To install the SuperEZ AI - Page/Post File Uploader Plugin via ZIP file:

1. Download the plugin zip file.
2. Log in to your WordPress admin dashboard.
3. Go to the "Plugins" section.
4. Click on "Add New."
5. Click on the "Upload Plugin" button.
6. Upload the plugin zip file.
7. Activate the plugin.

## 6. Customization

As of now, the plugin includes CSS and JavaScript directly in the PHP code. Future developments plan to move these assets into external files for better organization and maintainability.

## 7. Future Developments

The developer has plans to further enhance this plugin by:

- Adding a settings page for template customization.
- Getting the zip file up for ez install to WP plugin directory

## 8. Notes

- The media uploader currently works only for admin users (as set in the code). Consider customizing this setting according to your needs.

Please refer to the [plugin's GitHub repository](https://github.com/g023/SuperEZ-AI-Page-Post-File-Uploader) for more information, updates, and support.

Feel free to contribute, report issues, or suggest improvements to make the SuperEZ AI - Page/Post File Uploader even more powerful and user-friendly.

 ![Alt text](/relative/path/to/img.jpg?raw=true "Optional Title")
