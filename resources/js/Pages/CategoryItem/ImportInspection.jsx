import React, { useMemo, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import CustomButton from '@/Components/CustomButton';

const ImportInspection = ({ auth, team, typeInspectionId, preview, result, warnings }) => {
    const [file, setFile] = useState(null);
    const [confirm, setConfirm] = useState(false);

    const teamName = team?.name ?? auth?.team?.name ?? 'Site';

    const previewItems = useMemo(() => {
        return preview?.items ?? [];
    }, [preview]);

    const handlePreview = () => {
        if (!file) {
            alert('Please choose a CSV file first.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        router.post(route('category-items.import.inspection.preview'), formData, {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    const handleApply = () => {
        if (!file) {
            alert('Please choose the same CSV file again before applying.');
            return;
        }
        if (!confirm) {
            alert('Please confirm you understand the warnings before applying.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('confirm', '1');

        router.post(route('category-items.import.inspection.apply'), formData, {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    const formatDetails = (details) => {
        const max = 8;
        const list = details ?? [];
        const shown = list.slice(0, max);
        const remaining = list.length - shown.length;
        return `${shown.join(', ')}${remaining > 0 ? ` ... (+${remaining} more)` : ''}`;
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Import Inspection Elements" />

            <div className="container mx-auto py-6">
                <header className="mb-6 flex items-center justify-between w-[95%]">
                    <div className="flex flex-col items-right">
                        <h1 className="view-header">
                            Import Elements <small>{teamName}</small>
                        </h1>
                        <div className="text-sm text-gray-500">
                            This importer targets <span className="font-medium">type_inspection_id={typeInspectionId}</span> for the active site.
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <Link
                            href={route('category-items.index', { data: 1 })}
                            className="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-xl button-text1"
                        >
                            Back
                        </Link>
                    </div>
                </header>

                <div className="bg-white rounded-lg p-6 space-y-6">
                    <div className="flex flex-col lg:flex-row gap-3">
                        <a
                            href={route('category-items.import.inspection.template')}
                            className="bg-[#F6C546] hover:bg-[#f8bf2d] px-4 py-2 rounded-xl button-text1 text-center"
                        >
                            Download Template (CSV)
                        </a>
                        <a
                            href={route('category-items.import.inspection.export')}
                            className="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl button-text1 text-center"
                        >
                            Export Current (CSV)
                        </a>
                    </div>

                    <div className="border rounded-lg p-4">
                        <div className="font-semibold mb-2">Upload</div>
                        <div className="text-sm text-gray-600 mb-3">
                            Format: row-per-detail with columns <span className="font-mono">item_name</span> and <span className="font-mono">detail_name</span>.
                        </div>

                        <input
                            type="file"
                            accept=".csv,.txt"
                            onChange={(e) => setFile(e.target.files?.[0] ?? null)}
                            className="block w-full"
                        />

                        <div className="mt-4 flex gap-3">
                            <CustomButton
                                title="Preview"
                                btnColor="bg-primary"
                                hoverColor="bg-primary-dark"
                                hoverTextColor="text-white"
                                onClick={handlePreview}
                            />
                            <div className="text-sm text-gray-500 flex items-center">
                                Preview validates the sheet and shows warnings. Apply will ask for the file again.
                            </div>
                        </div>
                    </div>

                    {Array.isArray(warnings) && warnings.length > 0 && (
                        <div className="border border-yellow-300 bg-yellow-50 rounded-lg p-4">
                            <div className="font-semibold mb-2">Warnings</div>
                            <ul className="list-disc ml-6 text-sm text-gray-700 space-y-1">
                                {warnings.map((w, idx) => (
                                    <li key={idx}>
                                        {w.type === 'db_duplicate_item_name' ? (
                                            <>
                                                Duplicate item name already exists in DB for key "<span className="font-mono">{w.key}</span>" (IDs: {w.ids.join(', ')}). Import will update the lowest non-deleted ID.
                                            </>
                                        ) : (
                                            JSON.stringify(w)
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {preview && (
                        <div className="border rounded-lg p-4 space-y-3">
                            <div className="font-semibold">Preview Summary</div>
                            <div className="text-sm text-gray-700">
                                Rows scanned: <span className="font-medium">{preview.total_rows}</span> · Items: <span className="font-medium">{preview.items_count}</span>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-4 py-2 table-header uppercase tracking-wider">Item</th>
                                            <th className="px-4 py-2 table-header uppercase tracking-wider">Details</th>
                                            <th className="px-4 py-2 table-header uppercase tracking-wider">Sample</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {previewItems.map((it, idx) => (
                                            <tr key={idx}>
                                                <td className="px-4 py-2 whitespace-nowrap table-text1">{it.item_name}</td>
                                                <td className="px-4 py-2 whitespace-nowrap table-text1">{it.details_count}</td>
                                                <td className="px-4 py-2 table-text1">{formatDetails(it.details)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className="flex items-center gap-3">
                                <label className="flex items-center gap-2 text-sm">
                                    <input type="checkbox" checked={confirm} onChange={(e) => setConfirm(e.target.checked)} />
                                    I understand this will import data into <span className="font-medium">{teamName}</span>.
                                </label>

                                <CustomButton
                                    title="Apply Import"
                                    btnColor="bg-primary"
                                    hoverColor="bg-primary-dark"
                                    hoverTextColor="text-white"
                                    onClick={handleApply}
                                />
                            </div>
                        </div>
                    )}

                    {result && (
                        <div className="border border-green-300 bg-green-50 rounded-lg p-4">
                            <div className="font-semibold mb-2">Import Result</div>
                            <div className="text-sm text-gray-700">
                                Items created: {result.items_created} · Items restored: {result.items_restored} · Details created: {result.details_created} · Details restored: {result.details_restored}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default ImportInspection;

