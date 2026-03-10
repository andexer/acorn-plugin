<?php

use Livewire\Component;

new class extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function reset(...$properties)
    {
        $this->count = 0;
    }
};
?>

<div class="max-w-sm mx-auto my-12">
    <flux:card class="space-y-8 p-10 bg-white/50 backdrop-blur-sm border-zinc-200/50 shadow-xl rounded-2xl">
        <div class="text-center space-y-2">
            <div class="text-6xl font-black text-zinc-900 tracking-tighter">{{ $count }}</div>
            <flux:subheading class="uppercase tracking-widest text-xs font-bold text-zinc-500">Global Counter</flux:subheading>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <flux:button wire:click="decrement" icon="minus" variant="primary" color="rose" class="py-4">
                Decrement
            </flux:button>

            <flux:button wire:click="increment" icon="plus" variant="primary" class="py-4">
                Increment
            </flux:button>

            <flux:button wire:click="reset" icon="arrow-path" variant="danger" class="col-span-2 py-3 mt-2">
                Reset to Zero
            </flux:button>
        </div>
    </flux:card>
</div>