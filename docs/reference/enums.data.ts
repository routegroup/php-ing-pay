import { readFileSync } from 'node:fs'
import { basename } from 'node:path'
import { defineLoader } from 'vitepress'

export interface EnumCase {
  name: string
  value: string | number | null
  // The `//` comment trailing the case, if any.
  note: string | null
}

export interface EnumType {
  name: string
  backing: 'string' | 'int' | null
  doc: string | null
  cases: EnumCase[]
}

declare const data: EnumType[]
export { data }

// Every enum in src/Types, read as text so the docs build needs Node only.
// The grammar used there is small and regular; anything outside it throws
// rather than drops a case from the page silently.
export default defineLoader({
  watch: ['../../src/Types/*.php'],
  load(files) {
    if (files.length === 0) throw new Error('enums.data.ts: no files match src/Types/*.php')
    return files.map(parseEnum).sort((a, b) => a.name.localeCompare(b.name))
  },
})

const enumRE = /(?:\/\*\*((?:(?!\*\/)[\s\S])*)\*\/\s*)?^enum\s+(\w+)\s*(?::\s*(string|int))?\s*(?:implements[^{]*)?\{/m
const caseRE = /^\s*case\s+(\w+)\s*(?:=\s*('(?:[^'\\]|\\.)*'|-?\d+))?\s*;\s*(?:\/\/\s*(.*?))?\s*$/

function parseEnum(file: string): EnumType {
  const source = readFileSync(file, 'utf8')
  const where = `src/Types/${basename(file)}`

  const head = enumRE.exec(source)
  if (!head) throw new Error(`${where}: no enum declaration found`)

  const [, doc, name, backing] = head
  const cases = source
    .split('\n')
    .filter((line) => /^\s*case\s/.test(line))
    .map((line) => {
      const match = caseRE.exec(line)
      if (!match) throw new Error(`${where}: can't read \`${line.trim()}\``)

      const [, caseName, raw, note] = match
      return { name: caseName, value: literal(raw, backing), note: note || null }
    })

  if (cases.length === 0) throw new Error(`${where}: enum ${name} has no cases`)

  return { name, backing: (backing as EnumType['backing']) ?? null, doc: docText(doc), cases }
}

function literal(raw: string | undefined, backing: string | undefined): string | number | null {
  if (raw === undefined) return null
  if (backing === 'int') return Number(raw)
  return raw.slice(1, -1).replace(/\\(['\\])/g, '$1')
}

function docText(doc: string | undefined): string | null {
  if (!doc) return null
  const text = doc
    .split('\n')
    .map((line) => line.replace(/^\s*\*\s?/, '').trim())
    .join(' ')
    .trim()
  return text || null
}
