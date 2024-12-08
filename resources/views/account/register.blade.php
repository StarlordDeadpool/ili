<x-layout>
    <header>
        <nav class="navbar bg-primary">
            <div class="container">
                <x-navbar-brand />
            </div>
        </nav>
        <section class="bg-light w-100">
            <div class="w-100 border-light-subtle border-bottom">
                <section class="container d-flex align-items-center justify-content-end py-2">
                    <a href="/" class="btn btn-success btn-sm">
                        <i class="bi bi-arrow-left"></i>
                        Go Back
                        <i class="bi bi-house"></i>
                    </a>
                </section>
            </div>
        </section>
    </header>
    <main class="d-flex align-items-center justify-content-center w-100 bg-success-subtle">
        <div class="container d-flex py-5">
            <section class="d-flex w-100 h-100 align-items-center justify-content-center">
                <img src="{{ asset('images/signup-cartoon.png') }}" class="w-75 h-75" />
            </section>
            <section class="d-flex flex-column w-100 h-100 align-items-center justify-content-center">
                <div style="width: 470px;" class="card mx-auto p-3">
                    <div class="card-body">
                        <style>
                            .strength {
                                font-size: 1rem;
                                font-weight: bold;
                                margin-top: 10px;
                            }

                            .weak {
                                color: red;
                            }

                            .medium {
                                color: orange;
                            }

                            .strong {
                                color: green;
                            }
                        </style>
                        <form autocomplete="off" action="/account" method="POST">
                            @csrf
                            @method('POST')

                            <h3 class="text-body-secondary">Create Patron Account</h3>
                            <br>
                            <div class="mb-3 d-flex column-gap-3">
                                <section>
                                    <label for="first_name" class="form-label">First name</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        value="{{ old('first_name') ?? '' }}">
                                    @error('first_name')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </section>
                                <section>
                                    <label for="last_name" class="form-label">Last name</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        value="{{ old('last_name') ?? '' }}">
                                    @error('last_name')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </section>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input autocomplete="off" name="email" class="form-control" name="email"
                                    id="email" value="{{ old('email') ?? '' }}">
                                @error('email')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input autocomplete="off" type="password" class="form-control" name="password"
                                    id="password" value="{{ old('password') ?? '' }}">
                                @error('password')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                                <div id="strengthMessage" class="strength form-text"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    id="password_confirmation" value="{{ old('password_confirmation') ?? '' }}">
                                @error('password_confirmation')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="w-100 btn btn-primary px-3">Create patron account</button>
                            </div>
                            <p class="text-center mt-4">Already have an account? <a href="/login">Sign in</a>.</p>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <x-footer />
    <x-slot:script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const passwordInput = document.getElementById("password");
                const strengthMessage = document.getElementById("strengthMessage");

                passwordInput.addEventListener("input", () => {
                    const password = passwordInput.value;
                    const strength = checkPasswordStrength(password);

                    // Update message and style based on strength
                    if (strength === "Weak") {
                        strengthMessage.textContent = "Weak Password";
                        strengthMessage.className = "strength weak";
                    } else if (strength === "Medium") {
                        strengthMessage.textContent = "Medium Strength Password";
                        strengthMessage.className = "strength medium";
                    } else if (strength === "Strong") {
                        strengthMessage.textContent = "Strong Password";
                        strengthMessage.className = "strength strong";
                    } else {
                        strengthMessage.textContent = "";
                        strengthMessage.className = "strength";
                    }
                });

                function checkPasswordStrength(password) {
                    if (password.length === 0) return "";

                    let strengthScore = 0;

                    // Check password length
                    if (password.length >= 8) strengthScore++;

                    // Check for lowercase letters
                    if (/[a-z]/.test(password)) strengthScore++;

                    // Check for uppercase letters
                    if (/[A-Z]/.test(password)) strengthScore++;

                    // Check for numbers
                    if (/\d/.test(password)) strengthScore++;

                    // Check for special characters
                    if (/[\W_]/.test(password)) strengthScore++;

                    // Determine strength
                    if (strengthScore <= 2) return "Weak";
                    if (strengthScore === 3) return "Medium";
                    return "Strong";
                }
            });
        </script>
    </x-slot:script>
</x-layout>
