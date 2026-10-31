---
outline: [2, 2]
---

<script setup lang="ts">
import { data as enums } from './enums.data'
</script>

# Enums

Every enum in `Routegroup\IngPay\Types`, with the value each case sends to the
gateway. The tables are read from `src/Types/` when the site is built, so they
always match the released code.

<template v-for="type in enums" :key="type.name">
  <h2 :id="type.name" tabindex="-1">
    {{ type.name }}
    <a class="header-anchor" :href="`#${type.name}`" :aria-label="`Permalink to ${type.name}`">&#8203;</a>
  </h2>
  <p v-if="type.doc">{{ type.doc }}</p>
  <p>
    <code>Routegroup\IngPay\Types\{{ type.name }}</code>
    <template v-if="type.backing">, backed by <code>{{ type.backing }}</code></template>
    <template v-else>, a pure enum</template>.
  </p>
  <table>
    <thead>
      <tr>
        <th>Case</th>
        <th v-if="type.backing">Value</th>
        <th v-if="type.cases.some((c) => c.note)">Note</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="c in type.cases" :key="c.name">
        <td><code>{{ c.name }}</code></td>
        <td v-if="type.backing"><code>{{ type.backing === 'string' ? `'${c.value}'` : c.value }}</code></td>
        <td v-if="type.cases.some((c) => c.note)">{{ c.note }}</td>
      </tr>
    </tbody>
  </table>
</template>
