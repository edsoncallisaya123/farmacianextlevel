{{-- Imagen del producto: foto real si existe; si no, un dibujo según el tipo de producto. Uso: @include('partials.producto_imagen', ['p' => $producto]) --}}
@if($p->imagen_url)
    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}">
@else
@php
    $min = \Illuminate\Support\Str::lower($p->nombre);
    $tipo = 'caja';
    if (\Illuminate\Support\Str::contains($min, ['jarabe'])) { $tipo = 'jarabe'; }
    elseif (\Illuminate\Support\Str::contains($min, ['gel'])) { $tipo = 'bomba'; }
    elseif (\Illuminate\Support\Str::contains($min, ['alcohol'])) { $tipo = 'frasco'; }
    elseif (\Illuminate\Support\Str::contains($min, ['suero', 'sobre'])) { $tipo = 'sobre'; }
    elseif (\Illuminate\Support\Str::contains($min, ['vitamina c', 'tubo'])) { $tipo = 'tubo'; }
    elseif (\Illuminate\Support\Str::contains($min, ['barbijo', 'mascarilla'])) { $tipo = 'barbijo'; }
    $paletas = [['#3b82f6', '#1d4ed8'], ['#fb923c', '#c2410c'], ['#22c55e', '#15803d'], ['#f87171', '#b91c1c'], ['#a78bfa', '#6d28d9'], ['#22d3ee', '#0e7490']];
    [$c1, $c2] = $paletas[((int)$p->categoria_id) % count($paletas)];
    $gid = 'g' . $p->id . '_' . substr(md5(uniqid('', true)), 0, 6);
    $base = (string)\Illuminate\Support\Str::of($p->nombre)->before(' (');
    $pal = preg_split('/\s+/', trim($base));
    $l1 = mb_strtoupper(mb_substr($pal[0] ?? '', 0, 11));
    $l2 = mb_substr(implode(' ', array_slice($pal, 1, 2)), 0, 13);
@endphp
<svg class="prod-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{{ $p->nombre }}">
    <defs>
        <linearGradient id="{{ $gid }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{{ $c1 }}"/><stop offset="1" stop-color="{{ $c2 }}"/></linearGradient>
        <linearGradient id="{{ $gid }}v" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fbbf24"/><stop offset="1" stop-color="#92400e"/></linearGradient>
    </defs>
    <ellipse cx="100" cy="182" rx="62" ry="8" fill="#000" opacity=".14"/>
    @if($tipo === 'caja' || $tipo === 'barbijo')
        <polygon points="46,66 122,66 152,46 76,46" fill="#fff" opacity=".55"/>
        <polygon points="122,66 152,46 152,160 122,180" fill="{{ $c2 }}"/>
        <rect x="46" y="66" width="76" height="114" rx="4" fill="url(#{{ $gid }})"/>
        <rect x="55" y="80" width="58" height="62" rx="4" fill="#fff"/>
        @if($tipo === 'barbijo')
            <rect x="64" y="92" width="40" height="24" rx="9" fill="#9bd5f2"/>
            <path d="M68 99h32M68 104h32M68 109h32" stroke="#4aa3d1" stroke-width="1.6"/>
            <path d="M64 100c-8 0-8 10 0 10M104 100c8 0 8 10 0 10" stroke="#4aa3d1" stroke-width="1.6" fill="none"/>
            <text x="84" y="132" text-anchor="middle" font-size="9" font-weight="800" fill="#111">BARBIJO</text>
        @else
            <text x="84" y="100" text-anchor="middle" font-size="11" font-weight="800" fill="#111">{{ $l1 }}</text>
            <text x="84" y="113" text-anchor="middle" font-size="8" font-weight="700" fill="#374151">{{ $l2 }}</text>
            <path d="M80 120h8v-7h8v7h8v8h-8v7h-8v-7h-8z" fill="#16a34a" transform="translate(-4 -2)"/>
        @endif
        <rect x="55" y="150" width="58" height="6" rx="3" fill="#fff" opacity=".85"/>
        <rect x="55" y="162" width="36" height="5" rx="2.5" fill="#fff" opacity=".6"/>
    @elseif($tipo === 'frasco' || $tipo === 'jarabe')
        <rect x="78" y="22" width="44" height="22" rx="5" fill="{{ $tipo === 'jarabe' ? '#dc2626' : '#1e3a8a' }}"/>
        <rect x="86" y="42" width="28" height="14" fill="{{ $tipo === 'jarabe' ? '#7c2d12' : $c2 }}"/>
        <rect x="60" y="54" width="80" height="126" rx="16" fill="url(#{{ $gid }}{{ $tipo === 'jarabe' ? 'v' : '' }})"/>
        <rect x="60" y="90" width="80" height="56" fill="#fff"/>
        <text x="100" y="113" text-anchor="middle" font-size="12" font-weight="800" fill="#111">{{ $l1 }}</text>
        <text x="100" y="128" text-anchor="middle" font-size="9" font-weight="700" fill="#374151">{{ $l2 }}</text>
        <rect x="68" y="62" width="9" height="108" rx="4.5" fill="#fff" opacity=".28"/>
    @elseif($tipo === 'bomba')
        <rect x="94" y="38" width="12" height="30" fill="#374151"/>
        <rect x="80" y="26" width="44" height="14" rx="6" fill="#1f2937"/>
        <rect x="118" y="28" width="22" height="7" rx="3" fill="#1f2937"/>
        <rect x="66" y="64" width="68" height="116" rx="14" fill="url(#{{ $gid }})"/>
        <rect x="66" y="96" width="68" height="52" fill="#fff"/>
        <text x="100" y="118" text-anchor="middle" font-size="12" font-weight="800" fill="#111">{{ $l1 }}</text>
        <text x="100" y="133" text-anchor="middle" font-size="9" font-weight="700" fill="#374151">{{ $l2 }}</text>
        <rect x="74" y="72" width="8" height="100" rx="4" fill="#fff" opacity=".28"/>
    @elseif($tipo === 'sobre')
        <rect x="54" y="30" width="92" height="150" rx="6" fill="url(#{{ $gid }})"/>
        <path d="M54 38h92M54 172h92" stroke="#fff" stroke-width="2" stroke-dasharray="4 3" opacity=".8"/>
        <circle cx="100" cy="82" r="20" fill="#fff"/>
        <path d="M100 68c8 11 12 16 12 22a12 12 0 0 1-24 0c0-6 4-11 12-22z" fill="#38bdf8"/>
        <text x="100" y="130" text-anchor="middle" font-size="14" font-weight="800" fill="#fff">{{ $l1 }}</text>
        <text x="100" y="146" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">{{ $l2 }}</text>
    @else
        <rect x="76" y="22" width="48" height="24" rx="6" fill="#ea580c"/>
        <rect x="72" y="44" width="56" height="136" rx="10" fill="url(#{{ $gid }})"/>
        <rect x="72" y="84" width="56" height="62" fill="#fff"/>
        <text x="100" y="108" text-anchor="middle" font-size="11" font-weight="800" fill="#111">{{ $l1 }}</text>
        <text x="100" y="122" text-anchor="middle" font-size="9" font-weight="700" fill="#374151">{{ $l2 }}</text>
        <rect x="80" y="52" width="8" height="120" rx="4" fill="#fff" opacity=".3"/>
    @endif
</svg>
@endif
