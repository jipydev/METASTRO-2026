<label for="jabatan_id" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Jabatan</label>
<select id="jabatan_id" name="jabatan_id" x-model="jabatanId"
    class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
    <option value="">-- Tanpa Jabatan --</option>
    <template x-for="item in jabatanOptions" :key="item.id">
        <option :value="item.id" x-text="item.nama"></option>
    </template>
</select>
<x-input-error :messages="$errors->get('jabatan_id')" class="mt-1" />
