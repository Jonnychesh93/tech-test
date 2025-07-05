<script setup>
    import {useForm} from "@inertiajs/vue3";
    import {ref} from "vue";

    let errors = ref({});

    let form = useForm({
        file: null,
    });

    function submit() {
        errors.value = {};

        if (form.file && form.file.type !== 'text/csv') {
            errors.value['file'] = 'Please upload a valid CSV file.';
            return false;
        }

        form.post(route('store'));
    }
</script>

<template>
    <div class="h-screen bg-gray-300 p-12">
        <div class="rounded-lg bg-white px-8 py-4 shadow-lg">
            <h1 class="text-lg font-bold">Welcome to my Tech Test!</h1>
            <div class="flex items-center mt-4">
                <input
                    @input="form.file = $event.target.files[0]"
                    type="file"
                    accept=".csv"
                />
                <button
                    @click="submit"
                    class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                    :class="{'opacity-50 cursor-not-allowed': !form.file}"
                    :disabled="!form.file"
                >
                    Upload CSV
                </button>
            </div>
            <span
                v-if="errors.file || $page.props.errors.file"
                class="text-red-500"
            >
                {{errors.file}}
                {{ $page.props.errors.file}}
            </span>
        </div>
    </div>
</template>
