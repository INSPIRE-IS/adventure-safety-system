import React, { useEffect, useMemo, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import SearchBar from '@/Components/SearchBar';

const HelpCenterIndex = ({ auth, categories = [], searchTerm = '', selectedQuestionId = 0 }) => {
    const [search, setSearch] = useState(searchTerm || '');
    const [selectedId, setSelectedId] = useState(selectedQuestionId || 0);

    useEffect(() => {
        setSearch(searchTerm || '');
    }, [searchTerm]);

    useEffect(() => {
        setSelectedId(selectedQuestionId || 0);
    }, [selectedQuestionId]);

    const questionById = useMemo(() => {
        const map = new Map();
        categories.forEach((category) => {
            (category.questions || []).forEach((question) => {
                map.set(question.id, { ...question, category });
            });
        });
        return map;
    }, [categories]);

    const firstQuestion = useMemo(() => {
        for (const category of categories) {
            if (category.questions && category.questions.length > 0) {
                return { ...category.questions[0], category };
            }
        }
        return null;
    }, [categories]);

    const selectedQuestion = questionById.get(selectedId) || firstQuestion;

    const handleSearchChange = (e) => {
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        window.location.href = route('help-center.index', { search });
    };

    const getTeamLabel = (question) => {
        if (!question) return '';
        return question.team_id ? question.team_name : 'Generic (All Teams)';
    };

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Help Center" />
            <div className="container mx-auto py-6">
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <SearchBar
                        search={search}
                        handleSearchChange={(e) => handleSearchChange(e)}
                        handleSearchSubmit={handleSearchSubmit}
                    />
                </div>

                <header className="mb-6 flex items-center justify-between w-[95%]">
                    <div className="flex flex-col items-right">
                        <h1 className="view-header">Help Center</h1>
                    </div>
                </header>

                {categories.length === 0 ? (
                    <div className="bg-white rounded-lg p-6">
                        <p className="table-text2">No matching questions.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <aside className="lg:col-span-1 bg-white rounded-lg p-4 overflow-auto max-h-[70vh]">
                            {categories.map((category) => (
                                <div key={category.id} className="mb-6">
                                    <div className="table-header2 uppercase tracking-wider">{category.name}</div>
                                    <div className="mt-2 space-y-2">
                                        {category.questions.map((question) => {
                                            const isActive = selectedQuestion?.id === question.id;
                                            return (
                                                <button
                                                    key={question.id}
                                                    type="button"
                                                    onClick={() => setSelectedId(question.id)}
                                                    className={`w-full text-left rounded-xl px-3 py-3 border ${
                                                        isActive
                                                            ? 'bg-[#FBF9F7] border-[#E4E4E4]'
                                                            : 'bg-white border-transparent hover:bg-[#FBF9F7]'
                                                    }`}
                                                >
                                                    <div className="table-text1">{question.question}</div>
                                                    <div className="table-header2 mt-1">{getTeamLabel(question)}</div>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            ))}
                        </aside>

                        <section className="lg:col-span-2 bg-white rounded-lg p-6 min-h-[70vh]">
                            {!selectedQuestion ? (
                                <p className="table-text2">Select a question to view the answer.</p>
                            ) : (
                                <>
                                    <div className="flex flex-col gap-2">
                                        <div className="text-[#323B4B] font-manrope font-bold text-[18px] leading-[150%]">
                                            {selectedQuestion.question}
                                        </div>
                                        <div className="flex flex-wrap gap-2">
                                            <span className="table-header2">{selectedQuestion?.category?.name}</span>
                                            <span className="table-header2">&bull;</span>
                                            <span className="table-header2">{getTeamLabel(selectedQuestion)}</span>
                                        </div>
                                    </div>

                                    <div
                                        className="help-md mt-6"
                                        dangerouslySetInnerHTML={{ __html: selectedQuestion.answer_html || '' }}
                                    />
                                </>
                            )}
                        </section>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
};

export default HelpCenterIndex;
