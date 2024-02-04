import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

const Index = ({ auth, matches }) => {
    const [selectedSkillLevel, setSelectedSkillLevel] = useState('');
    const [selectedParticipant, setSelectedParticipant] = useState('');
    const [participantsFilter, setParticipantsFilter] = useState('');
    const [filteredMatches, setFilteredMatches] = useState([]);

    const handleFilterChange = (e) => {
        const { name, value } = e.target;
        if (name === 'skillLevel') {
            setSelectedSkillLevel(value);
        } else if (name === 'participant') {
            setSelectedParticipant(value);
        } else if (name === 'participantsFilter') {
            setParticipantsFilter(value);
        }
    };

    useEffect(() => {
        let tempMatches = [...matches];

        if (selectedSkillLevel) {
            tempMatches = tempMatches.filter(match => match.skill_level === selectedSkillLevel);
        }

        if (selectedParticipant) {
            tempMatches = tempMatches.filter(match => {
                const participantNames = match.participants.map(participant => participant.name);
                return participantNames.includes(selectedParticipant);
            });
        }

        if (participantsFilter) {
            tempMatches = tempMatches.filter(match => {
                const matchParticipants = match.participants.map(participant => participant.name.toLowerCase());
                return matchParticipants.some(name => name.includes(participantsFilter.toLowerCase()));
            });
        }

        setFilteredMatches(tempMatches);
    }, [selectedSkillLevel, selectedParticipant, participantsFilter, matches]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Matches</h2>}
        >
            <Head title="Matches" />
            <div className='py-12'>
                <div className='max-w-7xl mx-auto sm:px-6 lg:px-8'>
                    <h1 className="text-2xl font-bold mb-4">Scheduled Matches</h1>
                    <div className="overflow-hidden border border-gray-200 sm:rounded-lg">
                        <form className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Filter by Skill Level:</label>
                            <select
                                name="skillLevel"
                                value={selectedSkillLevel}
                                onChange={handleFilterChange}
                                className="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            >
                                <option value="">All</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>

                            <label className="block text-sm font-medium text-gray-700">Participants Filter:</label>
                            <input
                                type="text"
                                name="participantsFilter"
                                value={participantsFilter}
                                onChange={handleFilterChange}
                                className="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            />
                        </form>
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Skill Level
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start-End Time
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Created By
                                    </th>
                                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Participants
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {(selectedSkillLevel === '' && selectedParticipant === '' && participantsFilter === ''
                                    ? matches : filteredMatches).map(match => (
                                        <tr key={match.id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {match.name}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {match.skill_level}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {match.start_time} - {match.end_time}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {match.created_by.name}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {match.participants.length} Participants
                                                <ul className='border p-1'>
                                                    {match.participants.map(participant => (
                                                        <li key={participant.id}>
                                                            {participant.name} -{' '}
                                                            <span className="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                                {participant.totalMatches} Total Matches
                                                            </span>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </td>
                                        </tr>
                                    ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Index;
