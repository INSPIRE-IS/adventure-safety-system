import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import TypeQuestionModal from './TypeQuestionModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';

const TypeQuestionsIndex = ({auth, typeQuestions, roles, teams, can, searchTerm, sortColumn, sortDirection }) => {
    //const { auth } = usePage().props;
    //const { destroy } = useForm();
    console.log('auth on typequestion ', auth);
    const [search, setSearch] = useState(searchTerm);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState("create");
    const [selectedTypeQuestion, setSelectedTypeQuestion] = useState(null);

    const handleOpenModal = (mode, typeQuestions = null) => {
        console.log('mode ', mode);
        console.log('typeQuestions ', typeQuestions);
        setModalMode(mode);
        setSelectedTypeQuestion(typeQuestions);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedTypeQuestion(null);
    };

    const handleSearchChange = (e) => {
        console.log('value ', e.target.value);
        setSearch(e.target.value);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        // Redirect to the route with search query and other parameters
        console.log('search ', search);
        window.location.href = route('type-questions.index', {
            search,
            sort: sortColumn,
            direction: sortDirection
        });
    };

    const handleDelete = (typeQuestion) => {
        if (confirm("Are you sure you want to delete this question type?")) {
            //post(route("type-questions.destroy", typeQuestion), { _method: "DELETE",});
            router.delete(route('type-questions.destroy', typeQuestion), {preserveScroll: true});
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Question Types" />
            <div className="container mx-auto py-6">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <SearchBar search={search} handleSearchChange={(e) => handleSearchChange(e)} handleSearchSubmit={handleSearchSubmit} />
                {can.type_question_create && (
                <AddButton title='Add New Question Type' icon="./images/add-icon.svg" onClick={() => handleOpenModal("create")} />
                )}
            </div>
            <header className="mb-6 flex items-center justify-between w-[95%]">
                <div className="flex flex-col  items-right">               
                    <h1 className="view-header">Question Types</h1>                    
                </div>
            </header>

            {/* TypeQuestions Table */}
            <div className="overflow-x-auto bg-white rounded-lg">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 table-header uppercase tracking-wider">
                            <Link 
                                href={route('type-questions.index', { 
                                    sort: 'name', 
                                    direction: sortColumn === 'name' && sortDirection === 'asc' ? 'desc' : 'asc' 
                                })} 
                                className="flex flex-row justify-center items-center"
                            >
                                Question Name
                                <img src="./images/left-arrow-icon.svg" alt="filter" className="w-[16px] h-[15px] rounded-full ml-4" />
                            </Link>
                            </th>
                            <th className="px-6 py-3 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {typeQuestions?.data.map((typeQuestion) => (
                            <tr key={typeQuestion.id} className='h-[78px]' style={{borderBottomWidth: '12px', borderBottomColor: "#F8F8F8",}}>
                                <td className=" px-6 py-4 whitespace-nowrap items-center justify-center  table-text1 ">
                                    {typeQuestion.name}
                                </td>
                                <td className="px-6 py-4 flex flex-row items-center justify-end">
                                    {can.type_question_show && (
                                        <button
                                            onClick={() => handleOpenModal("view",typeQuestion)}
                                            className="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </button>                                        
                                    )}
                                    {can.type_question_edit && (
                                        <button
                                            onClick={() => handleOpenModal("edit",typeQuestion)}
                                            className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 mr-3"
                                        >
                                            <img src="./images/edit-icon.svg" alt="Edit" className="w-45 h-45" />
                                        </button>
                                    )}
                                    {can.type_question_delete && (
                                        <button
                                            onClick={() => handleDelete(typeQuestion)}
                                            className="w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-red-200"
                                        >
                                            <img src="./images/remove-icon.svg" alt="Remove" className="w-45 h-45" />
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            {/* Pagination Links */}
            <div className="mt-4">
                <Pagination links={typeQuestions?.links} />
            </div>            
            {/* Modal */}
            <TypeQuestionModal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                mode={modalMode}
                typeQuestion={selectedTypeQuestion}
                roles={roles}
                teams={teams}
            />
            </div>

        </AuthenticatedLayout>
    );
};

export default TypeQuestionsIndex;
