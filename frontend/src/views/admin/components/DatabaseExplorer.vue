<template>
    <div class="space-y-8">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Database Explorer</h1>
                <p class="text-slate-500 font-medium mt-1">Admin-level view of all core platform tables.</p>
            </div>
            <button
                @click="$emit('refresh')"
                class="self-start bg-slate-900 text-white px-5 py-3 rounded-2xl font-bold hover:bg-slate-700 transition-colors"
            >
                Refresh Data
            </button>
        </header>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div
                v-for="item in datasets"
                :key="item.key"
                class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm"
            >
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ item.label }}</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ item.count }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <details
                v-for="item in datasets"
                :key="`table-${item.key}`"
                class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden"
            >
                <summary class="cursor-pointer list-none px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <span class="font-black text-slate-900">{{ item.label }}</span>
                    <span class="text-xs font-black uppercase tracking-widest text-rose-600">{{ item.count }} rows</span>
                </summary>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[800px]">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    v-for="column in item.columns"
                                    :key="`${item.key}-${column}`"
                                    class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500"
                                >
                                    {{ column }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="(row, rowIndex) in item.rows"
                                :key="`${item.key}-${rowIndex}`"
                                class="hover:bg-slate-50/70"
                            >
                                <td
                                    v-for="column in item.columns"
                                    :key="`${item.key}-${rowIndex}-${column}`"
                                    class="px-4 py-3 text-slate-700 align-top max-w-xs break-words"
                                >
                                    {{ formatValue(row[column]) }}
                                </td>
                            </tr>
                            <tr v-if="item.rows.length === 0">
                                <td :colspan="Math.max(item.columns.length, 1)" class="px-4 py-8 text-center text-slate-400 font-bold">
                                    No rows found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    databaseData: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['refresh']);

const toTitle = (value) => value.replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase());

const datasets = computed(() => {
    const tables = props.databaseData?.tables || {};
    const counts = props.databaseData?.counts || {};

    return Object.entries(tables).map(([key, rows]) => {
        const safeRows = Array.isArray(rows) ? rows : [];
        const columns = safeRows.length > 0 ? Object.keys(safeRows[0]) : [];

        return {
            key,
            label: toTitle(key),
            rows: safeRows,
            columns,
            count: counts[key] ?? safeRows.length
        };
    });
});

const formatValue = (value) => {
    if (value === null || value === undefined) return '-';
    if (typeof value === 'object') return JSON.stringify(value);
    return value;
};
</script>
