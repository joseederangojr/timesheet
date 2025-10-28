import js from '@eslint/js';
import prettier from 'eslint-config-prettier/flat';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import globals from 'globals';
import typescript from 'typescript-eslint';

/** @type {import('eslint').Linter.Config[]} */
export default [
    js.configs.recommended,
    ...typescript.configs.recommended,
    {
        ...react.configs.flat.recommended,
        ...react.configs.flat['jsx-runtime'], // Required for React 17+
        languageOptions: {
            globals: {
                ...globals.browser,
            },
        },
        rules: {
            'react/react-in-jsx-scope': 'off',
            'react/prop-types': 'off',
            'react/no-unescaped-entities': 'off',
        },
        settings: {
            react: {
                version: 'detect',
            },
        },
    },
    {
        plugins: {
            'react-hooks': reactHooks,
        },
        rules: {
            // Core hook rules
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',
            //React Compiler
            'react-hooks/config': 'error',
            'react-hooks/error-boundaries': 'error',
            'react-hooks/component-hook-factories': 'error',
            'react-hooks/gating': 'error',
            'react-hooks/globals': 'error',
            'react-hooks/immutability': 'error',
            'react-hooks/preserve-manual-memoization': 'error',
            'react-hooks/purity': 'error',
            'react-hooks/refs': 'error',
            'react-hooks/set-state-in-effect': 'error',
            'react-hooks/set-state-in-render': 'error',
            'react-hooks/static-components': 'error',
            'react-hooks/unsupported-syntax': 'warn',
            'react-hooks/use-memo': 'error',
            'react-hooks/incompatible-library': 'warn',
        },
    },
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'tailwind.config.js',
            'coverage/**',
        ],
    },
    prettier, // Turn off all rules that might conflict with Prettier
];
