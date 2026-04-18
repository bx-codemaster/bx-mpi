<?php 
  defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

  if (basename($_SERVER['PHP_SELF']) == 'bx_mpi.php') {
?>
<style>
  /* PDF Catalog Admin Styles */
  .SumoSelect.format,
  .SumoSelect.main_font_type,
  .SumoSelect.header_font_type,
  .SumoSelect.footer_font_type {
      display: block;
  }

  .SumoSelect.customer_group_id > p.SlectBox,
  .SumoSelect.language_id > p.SlectBox,
  .SumoSelect.categories_id > p.SlectBox {
    margin-top: -26px;
  }

    .tabs .tab-nav {
      list-style: none; 
      padding: 0;
      display: flex;
      gap: 6px;
      margin:0;
    }
    .tabs .tab-nav li a {
      padding: 6px 10px;
      background: #f1f1f1;
      border: 1px solid #ccc;
      border-bottom: none;
      display: inline-block;
      border-radius: 4px 4px 0 0;
      text-decoration: none;
      color: #222;
    }
    .tabs .tab-nav li a.active {
      background: #AF417E;
      color: #fff;
      font-weight: bold;
    }
    .tabs .tab-content {
      border-top: 1px solid #ccc;
    }
    .tabs .tab-content > div {
      display: none;
      padding: 5px;
      border: 1px solid #ccc;
      background: #fff;
      border-top: none;
    }
    .tabs .tab-content > div.active {
      display: block;
    }

    /* Right-Sidebar: Initial alle ausblenden */
    .boxRight > div[id$="-right"] {
      display: none;
    }

    .boxRight .contentTable {
      border: 1px solid #ccc;
    }

    .boxRight .contentTable:nth-child(even) {
      margin-bottom: 5px;
      border-top: none;
    }

    .tableBoxCenter tr {
      border-left: 1px solid #aaa;
      border-right: 1px solid #aaa;
    }
    .inputBtnSection {
      white-space: nowrap;
    }
    #font_conversion .contentTable {
      margin-bottom: 5px;
    }
    a.file_list {
      font-weight: bold;
      color: #ffffffff !important;
      background-color: #6e9c25ff !important;
    }
    a.file_list:hover {
      font-weight: bold;
      color: #ffffff !important;
      background-color: #AF417E !important;
    }
    a.file_list.active {
      font-weight: bold;
      color: #ffffff !important;
      background-color: #AF417E !important;
    }
    
    .fixed_messageStack {
      display: none;
    }

    label.inline-block {
      display: inline-block;
      margin: 5px 0 2px 0;
      font-weight: bold;
    }
    label.inline-block span {
      font-weight: normal;
    }

    .cfg_select_option > label {
      min-width: 60px;
      text-align: center;
    }

    tr.dataTableRow:nth-child(2n+1) {
      background: none repeat scroll 0 0 rgba(175, 65, 126, 0.1);
    }


    /* Modal-Styling */
    #pdfModal {
      display: none; 
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }
    #pdfModal .modal-content {
      padding: 0; 
      border: 1px solid #aaa; 
      background-color: #fff; 
      font-family: Arial, sans-serif; 
      font-size: 14px; 
      margin: auto; 
      box-shadow: 0 2px 5px rgba(0,0,0,0.25);
      width: 400px; 
      margin-top: 10%;
      border-radius: 8px;
      overflow: hidden;
    }
    #pdfModal .modal-content h3 {
      margin: 0;
      padding: 8px 15px;
      background-color: #AF417E;
      color: white;
      font-size: 16px;
      font-weight: bold;
    }

    #pdfModal .modal-content > div {
      padding: 15px;
    }

    #pdfModal .modal-content > div > div {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    #pdfModal .modal-content > div > div > label,
    #pdfModal .modal-content > div > div > span {
      width: 120px; 
      flex-shrink: 0; 
      margin-right: 10px; 
      font-weight: bold;
    }
    #pdfModal .modal-content > div > div > input {
      flex-grow: 1; 
      padding: 6px; 
      border: 1px solid #aaa;
    }
    #pdfModal #result_output {
      color: #AF417E; 
      flex-grow: 1; 
      padding: 6px; 
      border: 1px solid #aaa; 
      text-align: center;
      background-color: #f9f9f9;
    }
    #pdfModal .close {
      color: white;
      float: right;
      font-size: 20px;
      font-weight: bold;
      cursor: pointer;
    }
    #pdfModal .close:hover,
    #pdfModal .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    .fixed_messageStack {
      /* 1. Aus dem Dokumentenfluss nehmen */
      position: fixed; 
      /* 2. Oben zentrieren */
      top: 88px; 
      left: 50%;
      transform: translateX(-50%); /* Zentriert den Container horizontal */
      /* 3. Über allen anderen Elementen anzeigen */
      z-index: 1000; 
      /* 4. Aussehen und Breite festlegen */
      width: 80%; /* Beispiel: Volle Breite */
      /* max-width: 800px; Optional: Maximale Breite für bessere Lesbarkeit */
      padding: 10px 0;
      text-align: center;    
      /* Wichtig: Standardmäßig ausgeblendet */
      display: none;
    }
    
    .error_message,
    .warning_message,
    .info_message,
    .success_message {
      margin-bottom: 2px;
      display: inline-block;
      width: 100%;
    }

    input.is-invalid {
      background-color: red;
      color: white;
    }

    .info-mpi-button {
      display: inline-block;
      min-width: 170px;
      text-align: center;
      padding: 4px 12px;
      border-radius: 3px;
     }
  </style>
  <?php } ?>