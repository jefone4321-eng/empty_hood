<?php

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validatePasswordLength(string $value, int $min = 6): ?string
{
    return strlen($value) < $min ? "Must be at least $min characters." : null;
}

function validatePasswordMatch(string $password, string $confirm): ?string
{
    return $password !== $confirm ? "Passwords do not match." : null;
}

// --- Signup form ---
function validateSignupInput(array $post): array
{
    $name = trim($post['name'] ?? '');
    $email = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';
    $confirmPassword = $post['confirm_password'] ?? '';

    $errors = [];

    if ($err = validateRequired($name, 'Name')) $errors['name'] = $err;

    if ($email === '') {
        $errors['email'] = "Email is required.";
    } elseif ($err = validateEmailFormat($email)) {
        $errors['email'] = $err;
    }

    if ($password === '') {
        $errors['password'] = "Password is required.";
    } elseif ($err = validatePasswordLength($password)) {
        $errors['password'] = $err;
    }

    if ($confirmPassword === '') {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($err = validatePasswordMatch($password, $confirmPassword)) {
        $errors['confirm_password'] = $err;
    }

    return [
        'errors' => $errors,
        'data' => ['name' => $name, 'email' => $email, 'password' => $password],
    ];
}

// --- Login form ---
function validateLoginInput(array $post): array
{
    $email = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';

    $errors = [];

    if ($email === '') {
        $errors['email'] = "Email is required.";
    } elseif ($err = validateEmailFormat($email)) {
        $errors['email'] = $err;
    }

    if ($password === '') {
        $errors['password'] = "Password is required.";
    }

    return [
        'errors' => $errors,
        'data' => ['email' => $email, 'password' => $password],
    ];
}

// --- Contact form ---
function validateContactInput(array $post): array
{
    $name = trim($post['name'] ?? '');
    $email = trim($post['email'] ?? '');
    $message = trim($post['message'] ?? '');

    $errors = [];

    if ($err = validateRequired($name, 'Name')) $errors['name'] = $err;

    if ($email === '') {
        $errors['email'] = "Email is required.";
    } elseif ($err = validateEmailFormat($email)) {
        $errors['email'] = $err;
    }

    if ($err = validateRequired($message, 'Message')) $errors['message'] = $err;

    return [
        'errors' => $errors,
        'data' => ['name' => $name, 'email' => $email, 'message' => $message],
    ];
}