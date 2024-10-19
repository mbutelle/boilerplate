module.exports = {
    client: {
        input: '/var/shared/openai_schema.json',
        output: {
            target: './services/client.ts',
            override: {
                mutator: {
                    path: './orval/mutator/CustomHttpClient.ts',
                    name: 'CustomHttpClient',
                },
            },
        },
    },
};
