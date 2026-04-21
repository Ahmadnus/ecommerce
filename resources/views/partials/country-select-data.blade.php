@if(isset($countries) && $countries->isNotEmpty())
    @php
        // نقوم بالتحويل لمصفوفة عادية داخل بلوك PHP صريح 100%
        $mappedData = [];
        foreach ($countries as $c) {
            $mappedData[] = [
                'id'           => $c->id,
                'name'         => $c->name,
                'name_en'      => $c->name_en ?? '',
                'calling_code' => $c->calling_code ?? '',
            ];
        }
    @endphp

    <script>
        // هنا نمرر المتغير الجاهز، ولن يضطر Blade لتحليل أي مصفوفات أو أقواس معقدة
        window.__countriesData = {!! json_encode($mappedData) !!};
    </script>
@endif