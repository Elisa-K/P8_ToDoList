# Guide pour contribuer au projet ToDoList App

Il est possible de contribuer au projet **ToDoList** en améliorant ou en ajoutant une fonctionnalité, en corrigeant des bugs... Pour cela, veuillez suivre le guide ci-dessous.

## Fork du projet
Documentation officielle de GitHub : https://docs.github.com/fr/get-started/quickstart/fork-a-repo#forking-a-repository

1. Accéder au dépot : https://github.com/Elisa-K/P8_ToDoList

2. Dans le coin supérieur droit de la page, cliquer sur **Fork**.

3. Suivez les étapes de la documentation officiel de GitHub.

4. Une copie du repository sera créée sur vote compte GitHub

## Contribuer au projet

Pour toute remonter de bug, ou de proposition de nouvelles fonctionnalités, ...Vous devez ouvrir une issue depuis ce lien : https://github.com/Elisa-K/P8_ToDoList/issues

1. Cliquez sur **New issue**

2. Renseigner un titre et une description de votre contribution

3. Assignez l'issue à votre compte GitHub

4. Assigner un label correspondant au type de votre contribution (feature, documentation, bug, ...)

5. Créer une branch en suivant la règle de nommage : ***feature/number_issue***
```bash
git checkout -b feature/12
```

6. Développer vos modifications

7. Utiliser les outils de qualité de code :
```bash
vendor/bin/phpstan analayse -l 9 src
vendor/bin/php-cs-fixer fix .
```

8. Respecter un taux de couverture de code d'au moins 70%
```bash
vendor/bin/phpunit
```

9. Commiter vos changement en indiquant dans le message le numéro de l'issue
```bash
git commit -m "pagination task list #12"
```
10. Utiliser SymfonyInsight pour vérifier la qualité de code (minimum avoir une médaille d'or)

## Envoi des modifications
Pour transférer votre code, vous devez créer une **Pull Request**. Indiquez les différents changements que vous avez appliqué en renseignant le numéro de l'issue.
Chaque modification seront analysés puis seront intégrées ou non au projet en fonction de leur pertinance.