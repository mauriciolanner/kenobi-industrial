<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="https://bomix.com.br/wp-content/themes/iwwa-bomix/img/logos/logo.png" class="logo" alt="Bomix Logo">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>