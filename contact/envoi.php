<?php
// Traitement du formulaire de contact EEPD Consulting
$lang = (($_POST['lang'] ?? 'fr') === 'en') ? 'en' : 'fr';
$ok_url = $lang === 'en' ? '/en/contact/thanks/' : '/contact/merci/';
$form_url = $lang === 'en' ? '/en/contact/' : '/contact/';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $form_url); exit; }
if (!empty($_POST['website'])) { header('Location: ' . $ok_url); exit; } // champ piège anti-spam
function clean($s, $max) { $s = trim((string)$s); $s = str_replace(["\r", "\n"], ' ', $s); return mb_substr($s, 0, $max); }
$nom = clean($_POST['nom'] ?? '', 100);
$email = trim((string)($_POST['email'] ?? ''));
$tel = clean($_POST['tel'] ?? '', 40);
$sujet = clean($_POST['sujet'] ?? '', 80);
$msg = mb_substr(trim((string)($_POST['message'] ?? '')), 0, 5000);
if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '' || empty($_POST['consent'])) {
    header('Location: ' . $form_url . '?erreur=1'); exit;
}
$to = 'info@eepd-consulting.com';
$subject = '=?UTF-8?B?' . base64_encode('[Site EEPD] ' . ($sujet !== '' ? $sujet : 'Demande') . ' - ' . $nom) . '?=';
$body = "Nom : $nom\nEmail : $email\nTéléphone : $tel\nSujet : $sujet\nLangue : $lang\n\n$msg\n";
$headers = ['From: EEPD Consulting <no-reply@eepd-consulting.com>', 'Reply-To: ' . $email, 'Content-Type: text/plain; charset=UTF-8'];
$sent = mail($to, $subject, $body, implode("\r\n", $headers));
header('Location: ' . ($sent ? $ok_url : $form_url . '?erreur=2'));
exit;
