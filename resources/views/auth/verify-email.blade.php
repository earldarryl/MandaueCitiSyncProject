<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
        If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="mt-4 flex items-center justify-between">
            <button type="submit" class="text-sm text-blue-600 hover:underline">
                Resend Verification Email
            </button>
        </div>
    </form>
</x-guest-layout>
