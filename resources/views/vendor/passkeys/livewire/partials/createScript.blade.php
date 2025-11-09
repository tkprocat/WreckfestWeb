@script
<script>
    console.log('Passkey script loaded');
    console.log('startRegistration available:', typeof window.startRegistration);
    console.log('Secure context (HTTPS/localhost):', window.isSecureContext);
    console.log('WebAuthn API available:', typeof window.PublicKeyCredential !== 'undefined');
    console.log('Current URL:', window.location.href);

    $wire.on('passkeyPropertiesValidated', async (eventData) => {
        console.log('passkeyPropertiesValidated event received', eventData);
        const passkeyOptions = eventData[0].passkeyOptions;
        console.log('Passkey options:', passkeyOptions);

        try {
            const passkey = await startRegistration({ optionsJSON: passkeyOptions });
            console.log('Passkey created:', passkey);

            $wire.call('storePasskey', JSON.stringify(passkey));
        } catch (error) {
            console.error('Error creating passkey:', error);
        }
    });
</script>
@endscript
