<script setup lang="ts">
import { ref, toRef, watch } from 'vue';
import { Field, useField, defineRule } from 'vee-validate';
import InputError from './InputError.vue';
import { required } from '@vee-validate/rules';

defineRule('required', required);

interface TextareaInputProps {
    name: string;
    placeholder?: string;
    maxlength?: number;
    rows?: number;
    modelValue: string;
    rules?: string | Record<string, unknown>;
}

const props = withDefaults(defineProps<TextareaInputProps>(), {
    placeholder: undefined,
    maxlength: undefined,
    rows: 4,
    rules: undefined
});

const emit = defineEmits(['update:modelValue']);

function emitValue(event: Event): void {
    const target = event.target as HTMLTextAreaElement;
    emit('update:modelValue', target.value);
}

const { value: inputValue } = useField(props.name, props.rules, {
    validateOnMount: false,
    initialValue: props.modelValue,
});

const modelValue = toRef(props, 'modelValue');

watch(modelValue, (newValue) => {
    inputValue.value = newValue;
});
</script>

<template>
    <Field v-slot="{ errors, field }" :name="name" :rules="rules" :validate-on-input="true">
        <textarea
            :class="[
                'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                errors.length > 0 ? 'border-red-500' : '',
            ]"
            v-bind="{ ...$attrs, ...field }"
            :placeholder="placeholder"
            :maxlength="maxlength"
            :rows="rows"
            :value="modelValue"
            @input="emitValue"
        />

        <InputError class="mt-2" :message="errors[0]" />
    </Field>
</template>
