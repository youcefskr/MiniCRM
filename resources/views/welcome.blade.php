<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Koala CRM - Your CRM, Simplified</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2ECC71",
                        "background-light": "#FFFFFF",
                        "background-dark": "#102210",
                        "text-light": "#2C3E50",
                        "text-dark": "#F8F9FA",
                        "accent-light": "#F8F9FA",
                        "accent-dark": "#1a2c1a",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
    <div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <div class="flex flex-1 justify-center">
                <div class="layout-content-container flex flex-col w-full max-w-6xl flex-1">
                    <!-- TopNavBar -->
                    <header
                        class="flex items-center justify-between whitespace-nowrap px-4 sm:px-10 py-4 border-b border-accent-light dark:border-accent-dark sticky top-0 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm z-50">
                        <div class="flex items-center gap-3">
                            <div class="text-primary">
                                <svg class="w-7 h-7" fill="none" viewbox="0 0 48 48"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                        fill="currentColor"></path>
                                </svg>
                            </div>
                            <h2 class="text-text-light dark:text-text-dark text-xl font-bold leading-tight">Koala</h2>
                        </div>
                        <div class="hidden md:flex flex-1 justify-center items-center gap-8">
                            <a class="text-text-light dark:text-text-dark text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Features</a>
                            <a class="text-text-light dark:text-text-dark text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Pricing</a>
                            <a class="text-text-light dark:text-text-dark text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Contact</a>
                        </div>
                        <div class="flex items-center gap-2">
                            @if (Route::has('login'))
                                <nav class="flex items-center justify-end gap-4">
                                    @auth
                                        <button
                                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-transparent text-text-light dark:text-text-dark text-sm font-bold leading-normal tracking-[0.015em] hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
                                            <a href="{{ url('/dashboard') }}" class="">
                                                Dashboard
                                            </a>
                                        </button>
                                    @else
                                        <button
                                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-transparent text-text-light dark:text-text-dark text-sm font-bold leading-normal tracking-[0.015em] hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
                                            <a href="{{ route('login') }}" class="">
                                                Log in
                                            </a>
                                        </button>

                                        @if (Route::has('register'))
                                            <button
                                                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:opacity-90 transition-opacity">
                                                <a href="{{ route('register') }}" class="">
                                                    Register
                                                </a>
                                            </button>
                                        @endif
                                    @endauth
                                </nav>
                            @endif


                        </div>
                    </header>
                    <main>
                        <!-- HeroSection -->
                        <div class="@container">
                            <div
                                class="flex flex-col gap-6 px-4 py-16 sm:py-24 @[864px]:flex-row @[864px]:items-center">
                                <div
                                    class="flex flex-col gap-6 text-center @[864px]:text-left @[864px]:w-1/2 @[864px]:gap-8">
                                    <div class="flex flex-col gap-4">
                                        <h1
                                            class="text-text-light dark:text-text-dark text-4xl font-black leading-tight tracking-tighter @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight lg:text-6xl">
                                            Your CRM, Simplified
                                        </h1>
                                        <h2
                                            class="text-text-light/80 dark:text-text-dark/80 text-base font-normal leading-normal @[480px]:text-lg">
                                            Koala CRM helps you manage customer relationships effortlessly, streamline
                                            your workflow, and grow your business.
                                        </h2>
                                    </div>
                                    <button
                                        class="flex self-center @[864px]:self-start min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90 transition-opacity">
                                        <span class="truncate">Get Started for Free</span>
                                    </button>
                                </div>
                                <div class="w-full @[864px]:w-1/2">
                                    <img class="aspect-video w-full rounded-xl object-cover"
                                        data-alt="A person interacting with a modern point-of-sale system, representing streamlined workflows in a business."
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB0IxdLSmmGoAIdvxiaP4UX-WjP3dbFuRFQz-N5NrfQPfFSw8eNKZ3KzZeHjH3emnX8CpZATgNLf_qKbzxDuRPjZ4jxWOh5wZpbKQC1P0ITPPRW9I2K8iT4cwJ_RGSrPTlopcc1DS0oQBy2EliLvCdXg2xWDs87LLrArIhuSQsMxIIW3vxDPhgA9w0NkbZxhy5E1H82Ayv9i6gzLt--PEJnWMUSPrFLpFXntJzYVoMPy8h1M4EuaJ3AgO9df6ydRQsOEYEaN9OJk8XW" />
                                </div>
                            </div>
                        </div>
                        <!-- FeatureSection -->
                        <div class="bg-accent-light dark:bg-accent-dark">
                            <div class="max-w-6xl mx-auto flex flex-col gap-10 px-4 py-16 sm:py-24 @container">
                                <div class="flex flex-col gap-4 text-center">
                                    <h1
                                        class="text-text-light dark:text-text-dark text-3xl font-bold leading-tight tracking-tight @[480px]:text-4xl @[480px]:font-black">
                                        Why Choose Koala?
                                    </h1>
                                    <p
                                        class="text-text-light/80 dark:text-text-dark/80 text-base font-normal leading-normal max-w-2xl mx-auto">
                                        Discover the powerful features that make managing customer relationships easier
                                        and more effective than ever.
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-0">
                                    <div
                                        class="flex flex-1 gap-4 rounded-xl bg-background-light dark:bg-background-dark p-6 flex-col text-center items-center">
                                        <div class="text-primary bg-primary/20 rounded-full p-3">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 32px;">dashboard</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <h2
                                                class="text-text-light dark:text-text-dark text-lg font-bold leading-tight">
                                                Intuitive Dashboard</h2>
                                            <p
                                                class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">
                                                Get a clear overview of your sales pipeline and customer interactions in
                                                one simple view.</p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex flex-1 gap-4 rounded-xl bg-background-light dark:bg-background-dark p-6 flex-col text-center items-center">
                                        <div class="text-primary bg-primary/20 rounded-full p-3">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 32px;">autorenew</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <h2
                                                class="text-text-light dark:text-text-dark text-lg font-bold leading-tight">
                                                Automate Your Workflow</h2>
                                            <p
                                                class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">
                                                Save time by automating repetitive tasks, follow-ups, and data entry.
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex flex-1 gap-4 rounded-xl bg-background-light dark:bg-background-dark p-6 flex-col text-center items-center">
                                        <div class="text-primary bg-primary/20 rounded-full p-3">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 32px;">extension</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <h2
                                                class="text-text-light dark:text-text-dark text-lg font-bold leading-tight">
                                                Seamless Integration</h2>
                                            <p
                                                class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">
                                                Connect Koala with your favorite tools to create a unified business
                                                ecosystem.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Social Proof/Testimonial Section -->
                        <div class="max-w-6xl mx-auto px-4 py-16 sm:py-24 text-center">
                            <div class="max-w-3xl mx-auto flex flex-col items-center gap-6">
                                <img class="w-24 h-24 rounded-full object-cover"
                                    data-alt="Portrait of a smiling professional woman."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-1Baoi2_Q6Eq_Ups-ARc8MKRQlSv7BkK1o5oMrLFechXHx2BDqPOR31z8yFX9hnnYOJZ6DPoWRcovRcJsyZ1cwfobWx_0WvRJ1rnIZsdUqE1no93ALn_Ak0K9Dw-4OfIO5vc5c7_AV9gtMyzNta6sZcRfK9tPXyI6OH6YGJw8oXYA00OTdAMqtZBNM33rEZ2lnbbqm5_FJs1WDbCSUwSCra_AWg0sSv6-M-pZ_gkG7OgNHRlSHRd5I_qIh9K2ty7R5SmbefSjxy9D" />
                                <blockquote class="text-xl italic text-text-light/90 dark:text-text-dark/90">
                                    "Koala CRM has transformed the way we handle customer relations. It's intuitive,
                                    powerful, and has become an indispensable tool for our sales team."
                                </blockquote>
                                <div>
                                    <p class="font-bold text-text-light dark:text-text-dark">Jane Doe</p>
                                    <p class="text-sm text-text-light/70 dark:text-text-dark/70">CEO, Innovate Inc.</p>
                                </div>
                            </div>
                        </div>
                        <!-- CTASection -->
                        <div class="bg-accent-light dark:bg-accent-dark">
                            <div class="max-w-6xl mx-auto @container">
                                <div
                                    class="flex flex-col items-center justify-end gap-6 px-4 py-16 text-center sm:py-24 @[480px]:gap-8">
                                    <div class="flex flex-col gap-2">
                                        <h1
                                            class="text-text-light dark:text-text-dark text-3xl font-bold leading-tight tracking-tight @[480px]:text-4xl @[480px]:font-black">
                                            Ready to simplify your customer relationships?
                                        </h1>
                                        <p
                                            class="text-text-light/80 dark:text-text-dark/80 text-base font-normal leading-normal max-w-xl">
                                            Join thousands of businesses growing with Koala CRM. Sign up today for a
                                            free trial, no credit card required.
                                        </p>
                                    </div>
                                    <div class="flex justify-center">
                                        <button
                                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90 transition-opacity">
                                            <span class="truncate"><a href="{{ route('register') }}" class="">
                                                    SignUp now
                                                </a></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    <!-- Footer -->
                    <footer class="flex flex-col gap-8 px-5 py-10 text-center @container">
                        <div
                            class="flex flex-wrap items-center justify-center gap-x-6 gap-y-4 @[480px]:flex-row @[480px]:justify-center">
                            <a class="text-text-light/70 dark:text-text-dark/70 text-sm font-normal leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">About Us</a>
                            <a class="text-text-light/70 dark:text-text-dark/70 text-sm font-normal leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Careers</a>
                            <a class="text-text-light/70 dark:text-text-dark/70 text-sm font-normal leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Privacy Policy</a>
                            <a class="text-text-light/70 dark:text-text-dark/70 text-sm font-normal leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="#">Terms of Service</a>
                        </div>
                        <div class="flex flex-wrap justify-center gap-6">
                            <a href="#">
                                <svg aria-hidden="true"
                                    class="w-6 h-6 text-text-light/70 dark:text-text-dark/70 hover:text-primary dark:hover:text-primary transition-colors"
                                    fill="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84">
                                    </path>
                                </svg>
                            </a>
                            <a href="#">
                                <svg aria-hidden="true"
                                    class="w-6 h-6 text-text-light/70 dark:text-text-dark/70 hover:text-primary dark:hover:text-primary transition-colors"
                                    fill="currentColor" viewbox="0 0 24 24">
                                    <path clip-rule="evenodd"
                                        d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.206v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.337 8.905H4.002v-8.59h2.672v8.59zM17.638 2H6.362A4.362 4.362 0 002 6.362v11.276A4.362 4.362 0 006.362 22h11.276A4.362 4.362 0 0022 17.638V6.362A4.362 4.362 0 0017.638 2z"
                                        fill-rule="evenodd"></path>
                                </svg>
                            </a>
                            <a href="#">
                                <svg aria-hidden="true"
                                    class="w-6 h-6 text-text-light/70 dark:text-text-dark/70 hover:text-primary dark:hover:text-primary transition-colors"
                                    fill="currentColor" viewbox="0 0 24 24">
                                    <path clip-rule="evenodd"
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                        fill-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        <form id="chat-form">
                            <input type="text" id="message" placeholder="Type your message..." />
                            <button type="submit">Send</button>
                        </form>

                        <p class="text-text-light/70 dark:text-text-dark/70 text-sm font-normal leading-normal">© 2024
                            Koala CRM. All rights reserved.</p>
                    </footer>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
