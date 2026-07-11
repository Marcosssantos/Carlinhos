<?php
$conn = new mysqli("localhost","root","","cp_jardinagem");
if($conn->connect_error) die("Erro DB");
session_start();
