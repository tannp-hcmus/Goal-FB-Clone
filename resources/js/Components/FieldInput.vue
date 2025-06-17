<script setup lang="ts">
import { onMounted, ref, toRef, watch } from 'vue';
import { Field, useField, defineRule } from 'vee-validate';
import InputError from './InputError.vue';
import { required, numeric, digits, min, email } from '@vee-validate/rules';

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
defineRule('required', required);
defineRule('numeric', numeric);
defineRule('digits', digits);
defineRule('min', min);
defineRule('email', email);

interface FieldInputProps {
    type?: string;
    name: string;
    placeholder?: string;
    maxlength?: number;
    inputmode?: 'email' | 'numeric' | 'url' | 'tel' | 'text' | 'search' | 'none' | 'decimal';
    modelValue: string | number;
    rules?: string | Record<string, unknown>;
    validationLabel?: string;
}

const props = withDefaults(defineProps<FieldInputProps>(), {
    type: 'text',
    placeholder: undefined,
    maxlength: undefined,
    inputmode: undefined,
    rules: undefined,
    validationLabel: undefined
});

const emit = defineEmits(['update:modelValue']);

function emitValue(event: Event): void {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
}

// Use validation label for better error messages
const fieldLabel = props.validationLabel || 'This field';

const { value: inputValue, resetField } = useField(props.name, props.rules, {
    validateOnMount: false,
    initialValue: props.modelValue,
});

const modelValue = toRef(props, 'modelValue');

watch(modelValue, (newValue) => {
    inputValue.value = newValue;
    if (!newValue && resetField) {
        try {
            resetField();
        } catch (error) {
            // Silently handle any reset errors
        }
    }
});

// Function to transform error message
const getErrorMessage = (originalMessage: string): string => {
    if (!originalMessage) return '';

    // Replace various patterns that might appear in validation messages
    let message = originalMessage;

    // Replace the field name with the label
    message = message.replace(new RegExp(props.name, 'gi'), fieldLabel);

    // Handle common validation message patterns
    if (message.includes('is not valid') || message.includes('is required')) {
        return `${fieldLabel} is required`;
    }

    return message;
};
</script>

<template>
    <Field v-slot="{ errors, field }" :name="name" :rules="rules" :validate-on-blur="true" :validate-on-change="false" :validate-on-input="false" :key="name">
        <input
            :class="[
                'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                errors.length > 0 ? 'border-red-500' : '',
            ]"
            v-bind="{ ...$attrs, ...field }"
            :type="type"
            :placeholder="placeholder"
            :maxlength="maxlength"
            :inputmode="inputmode"
            :value="modelValue"
            @input="emitValue"
        />

        <InputError class="mt-2" :message="errors[0] ? getErrorMessage(errors[0]) : ''" />
    </Field>
</template>
