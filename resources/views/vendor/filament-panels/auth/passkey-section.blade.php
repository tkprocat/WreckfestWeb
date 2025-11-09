<div class="mt-6 space-y-3">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">Or continue with</span>
        </div>
    </div>

    <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Passkey Authentication</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
            Sign in securely using a passkey stored in your password manager or device.
        </p>
        <x-passkeys::authenticate />
    </div>
</div>
