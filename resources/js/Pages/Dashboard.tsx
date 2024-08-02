import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { BallTriangle, FallingLines } from 'react-loader-spinner';

export default function Dashboard({ auth }: PageProps) {

    const [data, setData] = useState<Array<any>>([]);
    const [currentPage, setCurrentPage] = useState<number>(1);
    const [isLoading, setIsLoading] = useState<boolean>(true);

    useEffect(() => {

        axios.get(`/api/v1.1/admin/establishments/${currentPage}`, {
            headers: {
                "Authorization" : "Bearer " + localStorage.getItem("token"),
            }
        })        
            .then(response => {
                setData(response.data.data.data);
                setIsLoading(false);
            })
            .catch(error => {
                console.error('There was an error fetching the data!', error);
                setIsLoading(false);
        });
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="">
                {isLoading ? (
                    <>
                        <div className="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
                            <div className=" ">
                            <BallTriangle
                                height="105"
                                width="105"
                                color="#CA0B4A"
                                ariaLabel="loading"
                            />
                            </div>{" "}
                        </div>
                        <div className="fixed inset-0 z-40 bg-primary"></div>
                    </>
                ) : (
                    <div className="">
                        <div className="bg-white p-4">
                            <div className="p-6 text-gray-900">
                                <div>
                                    {data.map((establishment, index) => {
                                        return (
                                            <div key={index}>
                                                <p>
                                                    <span> Name Establishment</span>
                                                    <span> {establishment.nameEtablishment}</span>
                                                </p>
                                                <p>
                                                    <span> Adresse</span>
                                                    <span> {establishment.address}</span>
                                                </p>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
