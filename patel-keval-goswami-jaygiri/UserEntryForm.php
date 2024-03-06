<?php
include 'pizza_header.php'; ?>

<div class="container">
  <form id="userForm">
    <label for="userInput">Enter your first and last name:</label>
    <input type="text" id="userInput" name="userInput" required>
    <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
    <button type="submit">Next</button>
  </form>
</div>