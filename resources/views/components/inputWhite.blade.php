@props(['disabled' => false, 'required' => false, 'maxlength' => null, 'minlength' => null, 'min' => null, 'max' => null])

<input {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }} 
       {{ $maxlength ? 'maxlength='.$maxlength : '' }} 
       {{ $minlength ? 'minlength='.$minlength : '' }} 
       {{ $min ? 'min='.$min : '' }} 
       {{ $max ? 'max='.$max : '' }} 
       {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-cyan-300 focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50']) !!}>